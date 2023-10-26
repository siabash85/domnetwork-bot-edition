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
                        <h2 class="text-xl">ویرایش تعرفه</h2>
                    </div>

                    <Form ref="formRef" @submit="handleUpdate">
                        <div class="grid grid-cols-12 gap-4">
                            <div class="col-span-12">
                                <Field
                                    mode="passive"
                                    name="name"
                                    v-slot="{ field }"
                                    rules="required"
                                    label=" نام تعرفه"
                                >
                                    <v-text-field
                                        v-model="form.name"
                                        label="نام تعرفه"
                                        variant="solo-filled"
                                        size="large"
                                        v-bind="field"
                                        hide-details="auto"
                                    ></v-text-field>
                                </Field>
                                <div class="invalid-feedback d-block">
                                    <ErrorMessage name="name" />
                                </div>
                            </div>
                            <div class="col-span-12">
                                <Field
                                    mode="passive"
                                    name="content"
                                    v-slot="{ field }"
                                    rules="required"
                                    label=" متن تعرفه"
                                >
                                    <v-textarea
                                        v-model="form.content"
                                        label=" متن تعرفه"
                                        variant="solo-filled"
                                        size="large"
                                        v-bind="field"
                                        hide-details="auto"
                                        rows="12"
                                    ></v-textarea>
                                </Field>
                                <div class="invalid-feedback d-block">
                                    <ErrorMessage name="content" />
                                </div>
                            </div>
                            <div class="col-span-12">
                                <Field
                                    mode="passive"
                                    name="is_default"
                                    v-slot="{ field }"
                                    rules="required"
                                    label="تعرفه پیش فرض"
                                >
                                    <v-radio-group
                                        v-bind="field"
                                        v-model="form.is_default"
                                    >
                                        <template v-slot:label>
                                            <div>تعرفه پیش فرض</div>
                                        </template>
                                        <v-radio
                                            label="می باشد"
                                            value="1"
                                        ></v-radio>
                                        <v-radio
                                            label="نمی باشد"
                                            value="0"
                                        ></v-radio>
                                    </v-radio-group>
                                </Field>
                                <div class="invalid-feedback d-block">
                                    <ErrorMessage name="is_default" />
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

        <v-snackbar absolute v-model="visible_success_message" :timeout="20000">
            تعرفه با موفقیت ویرایش شد.
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
    content: null,
    is_default: "0",
});
const visible_success_message = ref(false);

const router = useRouter();
const route = useRoute();
const handleUpdate = async (event) => {
    const { valid } = await formRef.value.validate();
    if (valid) {
        loading.value = true;
        const form_data = new FormData();
        form_data.append("name", form.value.name);
        form_data.append("content", form.value.content);
        form_data.append("is_default", form.value.is_default);
        const { data } = await ApiService.put(
            `/api/panel/pricing/${route.params.id}`,
            form_data
        );
        if (data.status == 200) {
            visible_success_message.value = true;
            router.push({ name: "panel-pricing-index" });
        }
    }
};

const fetchData = async () => {
    let { data } = await ApiService.get(
        `/api/panel/pricing/${route.params.id}`
    );
    form.value.name = data.data.name;
    form.value.content = data.data.content;
    form.value.is_default = data.data.is_default.toString();
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
