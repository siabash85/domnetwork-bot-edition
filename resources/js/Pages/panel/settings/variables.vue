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
                        <h2 class="text-xl">تنظیمات</h2>
                    </div>

                    <Form ref="formRef" @submit="handleUpdate">
                        <div class="grid grid-cols-12 gap-4">
                            <div class="col-span-12 lg:col-span-6">
                                <Field
                                    mode="passive"
                                    name="card_number"
                                    v-slot="{ field }"
                                    label="شماره کارت"
                                >
                                    <v-text-field
                                        v-model="form.card_number"
                                        label="شماره کارت"
                                        variant="solo-filled"
                                        size="large"
                                        v-bind="field"
                                        hide-details="auto"
                                    ></v-text-field>
                                </Field>
                                <div class="invalid-feedback d-block">
                                    <ErrorMessage name="card_number" />
                                </div>
                            </div>
                            <div class="col-span-12 lg:col-span-6">
                                <Field
                                    mode="passive"
                                    name="card_name"
                                    v-slot="{ field }"
                                    label="نام و نام خانوادگی شماره کارت"
                                >
                                    <v-text-field
                                        v-model="form.card_name"
                                        label="نام و نام خانوادگی شماره کارت"
                                        variant="solo-filled"
                                        size="large"
                                        v-bind="field"
                                        hide-details="auto"
                                    ></v-text-field>
                                </Field>
                                <div class="invalid-feedback d-block">
                                    <ErrorMessage name="card_name" />
                                </div>
                            </div>
                            <div class="col-span-12 lg:col-span-4">
                                <Field
                                    mode="passive"
                                    name="voucher_account_id"
                                    v-slot="{ field }"
                                    label="Voucher Member ID"
                                >
                                    <v-text-field
                                        v-model="form.voucher_account_id"
                                        label="Voucher Member ID"
                                        variant="solo-filled"
                                        size="large"
                                        v-bind="field"
                                        hide-details="auto"
                                    ></v-text-field>
                                </Field>
                                <div class="invalid-feedback d-block">
                                    <ErrorMessage name="voucher_account_id" />
                                </div>
                            </div>
                            <div class="col-span-12 lg:col-span-4">
                                <Field
                                    mode="passive"
                                    name="voucher_pass"
                                    v-slot="{ field }"
                                    label="Voucher Password"
                                >
                                    <v-text-field
                                        v-model="form.voucher_pass"
                                        label="Voucher Password"
                                        variant="solo-filled"
                                        size="large"
                                        v-bind="field"
                                        hide-details="auto"
                                    ></v-text-field>
                                </Field>
                                <div class="invalid-feedback d-block">
                                    <ErrorMessage name="voucher_pass" />
                                </div>
                            </div>
                            <div class="col-span-12 lg:col-span-4">
                                <Field
                                    mode="passive"
                                    name="voucher_payee_account"
                                    v-slot="{ field }"
                                    label="Voucher Account"
                                >
                                    <v-text-field
                                        v-model="form.voucher_payee_account"
                                        label="Voucher Account"
                                        variant="solo-filled"
                                        size="large"
                                        v-bind="field"
                                        hide-details="auto"
                                    ></v-text-field>
                                </Field>
                                <div class="invalid-feedback d-block">
                                    <ErrorMessage
                                        name="voucher_payee_account"
                                    />
                                </div>
                            </div>
                            <div class="col-span-12 lg:col-span-4">
                                <Field
                                    mode="passive"
                                    name="required_channel"
                                    v-slot="{ field }"
                                    label="آیدی کانال"
                                >
                                    <v-text-field
                                        v-model="form.required_channel"
                                        label="آیدی کانال"
                                        variant="solo-filled"
                                        size="large"
                                        v-bind="field"
                                        hide-details="auto"
                                    ></v-text-field>
                                </Field>
                                <div class="invalid-feedback d-block">
                                    <ErrorMessage name="required_channel" />
                                </div>
                            </div>
                            <div class="col-span-12 lg:col-span-4">
                                <Field
                                    mode="passive"
                                    name="voucher_link"
                                    v-slot="{ field }"
                                    label="لینک خرید ووچر"
                                >
                                    <v-text-field
                                        v-model="form.voucher_link"
                                        label="لینک خرید ووچر"
                                        variant="solo-filled"
                                        size="large"
                                        v-bind="field"
                                        hide-details="auto"
                                    ></v-text-field>
                                </Field>
                                <div class="invalid-feedback d-block">
                                    <ErrorMessage name="voucher_link" />
                                </div>
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

        <v-snackbar absolute v-model="visible_success_message" :timeout="2000">
            با موفقیت ویرایش شد.
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
    card_number: null,
    card_name: null,
    voucher_link: null,
    required_channel: null,
    voucher_account_id: null,
    voucher_payee_account: null,
    voucher_pass: null,
});
const visible_success_message = ref(false);
const router = useRouter();
const handleUpdate = async (event) => {
    const { valid } = await formRef.value.validate();
    if (valid) {
        loading.value = true;
        const form_data = new FormData();
        form_data.append("card_number", form.value.card_number ?? "");
        form_data.append("card_name", form.value.card_name ?? "");
        form_data.append("voucher_link", form.value.voucher_link ?? "");
        form_data.append("required_channel", form.value.required_channel ?? "");
        form_data.append(
            "voucher_account_id",
            form.value.voucher_account_id ?? ""
        );
        form_data.append(
            "voucher_payee_account",
            form.value.voucher_payee_account ?? ""
        );
        form_data.append("voucher_pass", form.value.voucher_pass ?? "");

        const { data } = await ApiService.post(
            `/api/panel/setting/variables`,
            form_data
        );
        if (data.status == 200) {
            visible_success_message.value = true;
            loading.value = false;
        }
    }
};

const fetchData = async () => {
    let { data } = await ApiService.get(`/api/panel/setting/variables`);
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
