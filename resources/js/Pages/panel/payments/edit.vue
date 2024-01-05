<template>
    <div>
        <base-skeleton animated :loading="loader">
            <template #template>
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12 lg:col-span-12">
                        <base-skeleton-item
                            variant="card"
                            class="h-[500px]"
                        ></base-skeleton-item>
                    </div>
                </div>
            </template>
            <template #default>
                <v-sheet>
                    <div class="mb-6">
                        <h2 class="text-xl">ویرایش تراکنش</h2>
                    </div>

                    <Form ref="formRef" @submit="handleUpdate">
                        <div class="mb-6">
                            <v-chip
                                class="ma-2"
                                text-color="white"
                                prepend-icon="mdi-account-circle"
                            >
                                کاربر : {{ form.user?.username }}
                            </v-chip>

                            <v-chip
                                class="ma-2"
                                text-color="white"
                                append-icon="mdi-cash"
                            >
                                قیمت :‌ {{ form?.amount }} تومان
                            </v-chip>

                            <v-chip
                                class="ma-2"
                                text-color="white"
                                append-icon="mdi-credit-card-outline"
                            >
                                روش پرداخت : {{ form?.payment_method?.title }}
                            </v-chip>

                            <v-chip
                                class="ma-2"
                                text-color="white"
                                append-icon="mdi-anchor"
                            >
                                کد پیگیری : {{ form?.reference_code }}
                            </v-chip>

                            <template v-if="form?.status == 'pending'">
                                <v-chip
                                    class="ma-2"
                                    color="warning"
                                    text-color="white"
                                    prepend-icon="mdi-alert-circle-outline"
                                >
                                    وضعیت : در انتظار پرداخت
                                </v-chip>
                            </template>
                            <template v-if="form?.status == 'success'">
                                <v-chip
                                    class="ma-2"
                                    color="success"
                                    text-color="white"
                                    prepend-icon="mdi-alert-circle-outline"
                                >
                                    وضعیت : پرداخت موفق
                                </v-chip>
                            </template>
                            <template v-if="form?.status == 'rejected'">
                                <v-chip
                                    class="ma-2"
                                    color="red"
                                    text-color="white"
                                    prepend-icon="mdi-alert-circle-outline"
                                >
                                    وضعیت : لغو شده
                                </v-chip>
                            </template>

                            <v-chip
                                class="ma-2"
                                text-color="white"
                                prepend-icon="mdi-calendar-clock-outline"
                            >
                                تاریخ : {{ form?.created_at }}
                            </v-chip>
                        </div>

                        <div class="grid grid-cols-12 gap-4">
                            <div class="col-span-12">
                                <v-select
                                    v-model="form.status"
                                    label="انتخاب  وضعیت"
                                    :items="statuses"
                                    item-title="state"
                                    item-value="value"
                                    single-line
                                    variant="solo-filled"
                                ></v-select>
                            </div>
                        </div>

                        <div class="col-span-12">
                            <div class="mb-6">
                                <h2 class="text-xl">رسید پرداخت</h2>
                            </div>

                            <template v-if="form?.receipt">
                                <div>
                                    <img
                                        @click="OpenReceipt"
                                        class="w-[200px] h-[200px] cursor-pointer object-cover"
                                        :src="form?.receipt"
                                        alt=""
                                    />
                                </div>
                            </template>
                            <template v-else>
                                <h3 class="my-3">
                                    رسید پرداخت ارسال نشده است.
                                </h3>
                            </template>
                        </div>

                        <v-btn
                            :loading="loading"
                            color="light-blue-accent-4"
                            type="submit"
                            block
                            class="mt-2"
                            >ویرایش</v-btn
                        >
                    </Form>
                </v-sheet>
            </template>
        </base-skeleton>

        <v-snackbar absolute v-model="visible_success_message" :timeout="20000">
            تراکنش با موفقیت ویرایش شد.
        </v-snackbar>

        <v-dialog
            v-model="visible_receipt"
            transition="dialog-bottom-transition"
            width="auto"
        >
            <template v-slot:default="{ isActive }">
                <v-card>
                    <v-toolbar
                        color="primary"
                        title="تایید رسید پرداخت"
                    ></v-toolbar>
                    <v-card-text>
                        <div>
                            <img
                                class="w-[400px] h-[400px]"
                                :src="form?.receipt"
                                alt=""
                            />
                        </div>
                    </v-card-text>
                    <v-card-actions class="justify-end">
                        <v-btn variant="text" @click="isActive.value = false"
                            >تایید</v-btn
                        >
                    </v-card-actions>
                </v-card>
            </template>
        </v-dialog>
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watchEffect } from "vue";
import ApiService from "@/Core/services/ApiService";
import { useRoute, useRouter } from "vue-router";
import { ErrorMessage, Field, Form } from "vee-validate";
import { BaseSkeleton, BaseSkeletonItem } from "@/Components/skeleton";
const loader = ref(true);
const loading = ref(false);
const formRef = ref(null);
const form = ref({
    user: null,
    payment_method: null,
    reference_code: null,
    status: "active",
    amount: null,
    created_at: null,
});
const visible_receipt = ref(false);
const visible_success_message = ref(false);

const servers = ref([]);
const durations = ref([]);
const packages = ref([]);
const statuses = ref([
    { state: "در انتظار پرداخت", value: "pending" },
    { state: "پرداخت شده", value: "success" },
    { state: "لغو شده", value: "rejected" },
    { state: "در انتظار تایید رسید پرداخت", value: "pending_confirmation" },
]);
const router = useRouter();
const route = useRoute();
const handleUpdate = async (event) => {
    const { valid } = await formRef.value.validate();
    if (valid) {
        loading.value = true;
        const form_data = new FormData();
        form_data.append("status", form.value.status);
        const { data } = await ApiService.put(
            `/api/panel/payments/${route.params.id}`,
            form_data
        );
        if (data.status == 200) {
            visible_success_message.value = true;
            router.push({ name: "panel-payments-index" });
        }
    }
};
const OpenReceipt = () => {
    visible_receipt.value = true;
};

const fetchData = async () => {
    let { data } = await ApiService.get(
        `/api/panel/payments/${route.params.id}`
    );
    form.value = data.data;

    loader.value = false;
};
watchEffect(() => {
    if (formRef.value) {
        formRef.value.setValues({
            ...form.value,
        });
    }
});

onMounted(() => {
    fetchData();
});
</script>

<style scoped></style>
