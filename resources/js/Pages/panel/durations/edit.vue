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
                        <h2 class="text-xl">ویرایش بازه زمانی</h2>
                    </div>
                    <Form ref="formRef" @submit="handleUpdate">
                        <div class="grid grid-cols-12 gap-4">
                            <div class="col-span-12 lg:col-span-4">
                                <v-text-field
                                    type="number"
                                    v-model="form.name"
                                    :rules="rules"
                                    label="روز به عدد"
                                    density="compact"
                                    single-line
                                    hint="بازه زمانی را تعداد روز وارد کنید"
                                ></v-text-field>
                            </div>
                            <div class="col-span-12 lg:col-span-4">
                                <v-text-field
                                    type="number"
                                    v-model="form.value"
                                    label="مقدار"
                                    density="compact"
                                    single-line
                                    persistent-hint
                                    hint="مقدار 0 به معنی بی نهایت می باشد"
                                ></v-text-field>
                            </div>
                            <div class="col-span-12 lg:col-span-4">
                                <v-text-field
                                    type="number"
                                    v-model="form.price"
                                    label="مبلغ تمدید سرویس به ازای هر گیگابایت"
                                    density="compact"
                                    single-line
                                    persistent-hint
                                    hint=" مقدار وارد شده به تومان می باشد."
                                ></v-text-field>
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
            بازه زمانی با موفقیت ویرایش شد.
        </v-snackbar>
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
    name: null,
    value: null,
});
const visible_success_message = ref(false);
const rules = ref([
    (value) => {
        if (value) return true;
        return "نام  بازه زمانی  الزامی می باشد";
    },
]);
const router = useRouter();
const route = useRoute();
const handleUpdate = async (event) => {
    const { valid } = await formRef.value.validate();
    if (valid) {
        loading.value = true;
        const form_data = new FormData();
        form_data.append("name", form.value.name);
        form_data.append("value", form.value.value);
        form_data.append("price", form.value.price);
        const { data } = await ApiService.put(
            `/api/panel/package/durations/${route.params.id}`,
            form_data
        );
        if (data.status == 200) {
            visible_success_message.value = true;
            router.push({ name: "panel-durations-index" });
        }
    }
};

const fetchData = async () => {
    const { data } = await ApiService.get(
        `/api/panel/package/durations/${route.params.id}`
    );
    form.value.name = data.data.name;
    form.value.value = data.data.value;
    form.value.price = data.data.price;
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
