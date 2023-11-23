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
                        <h2 class="text-xl">ویرایش اشتراک</h2>
                    </div>

                    <Form ref="formRef" @submit="handleUpdate">
                        <div class="mb-6">
                            <v-chip
                                class="ma-2"
                                text-color="white"
                                prepend-icon="mdi-account-circle"
                            >
                                کاربر : {{ form.user?.username }} -
                                {{ form.user?.uid }}
                            </v-chip>

                            <v-chip
                                class="ma-2"
                                text-color="white"
                                append-icon="mdi-cash"
                            >
                                قیمت :‌
                                {{ form?.service?.price }} تومان
                            </v-chip>

                            <v-chip
                                class="ma-2"
                                text-color="white"
                                append-icon="mdi-anchor"
                            >
                                پکیج : {{ form?.service?.package?.name }}
                            </v-chip>

                            <v-chip
                                class="ma-2"
                                text-color="white"
                                append-icon="mdi-anchor"
                            >
                                سرور : {{ form?.service?.server?.name }}
                            </v-chip>
                            <v-chip
                                class="ma-2"
                                text-color="white"
                                append-icon="mdi-anchor"
                            >
                                زمان سرویس :
                                {{ form?.service?.package_duration?.value }} روز
                            </v-chip>

                            <v-chip
                                class="ma-2"
                                text-color="white"
                                append-icon="mdi-anchor"
                            >
                                نام سرویس : {{ form?.name }}
                            </v-chip>
                            <v-chip
                                class="ma-2"
                                text-color="white"
                                append-icon="mdi-anchor"
                            >
                                کد سرویس : {{ form?.code }}
                            </v-chip>
                            <v-chip
                                class="ma-2"
                                text-color="white"
                                append-icon="mdi-anchor"
                            >
                                شناسه سرویس : {{ form?.slug }}
                            </v-chip>

                            <template v-if="form?.status == 'active'">
                                <v-chip
                                    class="ma-2"
                                    color="success"
                                    text-color="white"
                                    prepend-icon="mdi-alert-circle-outline"
                                >
                                    وضعیت : فعال
                                </v-chip>
                            </template>
                            <template v-if="form?.status == 'inactive'">
                                <v-chip
                                    class="ma-2"
                                    color="red"
                                    text-color="white"
                                    prepend-icon="mdi-alert-circle-outline"
                                >
                                    وضعیت : غیرفعال
                                </v-chip>
                            </template>

                            <v-chip
                                class="ma-2"
                                text-color="white"
                                prepend-icon="mdi-calendar-clock-outline"
                            >
                                تاریخ ایجاد : {{ form?.created_at }}
                            </v-chip>
                            <v-chip
                                class="ma-2"
                                text-color="white"
                                prepend-icon="mdi-calendar-clock-outline"
                            >
                                تاریخ اتمام : {{ form?.expire_at }}
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
                                <h2 class="text-xl">لینک اشتراک</h2>
                            </div>

                            <div>
                                <v-chip
                                    @click="copyToClipboard"
                                    class="ma-2"
                                    color="indigo"
                                    prepend-icon="mdi-content-copy"
                                >
                                    {{ form?.config }}
                                </v-chip>
                            </div>
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
            اشتراک با موفقیت ویرایش شد.
        </v-snackbar>

        <v-snackbar absolute v-model="visible_snackbar" :timeout="2000">
            اشتراک با موفقیت کپی شد.
        </v-snackbar>
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watchEffect, watch } from "vue";
import ApiService from "@/Core/services/ApiService";
import { useRoute, useRouter } from "vue-router";
import { ErrorMessage, Field, Form } from "vee-validate";
import { BaseSkeleton, BaseSkeletonItem } from "@/Components/skeleton";
import { useClipboard } from "@vueuse/core";
const page_path = ref(null);
const { text, copy, copied, isSupported } = useClipboard({ page_path });

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
const visible_snackbar = ref(false);
const visible_receipt = ref(false);
const visible_success_message = ref(false);

const servers = ref([]);
const durations = ref([]);
const packages = ref([]);
const statuses = ref([
    { state: "فعال", value: "active" },
    { state: "غیرفعال", value: "inactive" },
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
            `/api/panel/subscriptions/${route.params.id}`,
            form_data
        );
        if (data.status == 200) {
            visible_success_message.value = true;
            router.push({ name: "panel-subscriptions-index" });
        }
    }
};
const copyToClipboard = () => {
    copy(form.value?.config);
};

const fetchData = async () => {
    let { data } = await ApiService.get(
        `/api/panel/subscriptions/${route.params.id}`
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

watch(
    () => copied.value,
    (val) => {
        if (val) {
            visible_snackbar.value = true;
        }
    }
);

onMounted(() => {
    fetchData();
});
</script>

<style scoped></style>
