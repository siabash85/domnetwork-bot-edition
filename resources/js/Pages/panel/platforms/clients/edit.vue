<template>
    <div>
        <v-sheet>
            <div class="mb-6">
                <h2 class="text-xl">ویرایش برنامه</h2>
            </div>

            <Form ref="formRef" @submit="handleCreate">
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-4">
                        <Field
                            mode="passive"
                            name="name"
                            v-slot="{ field }"
                            rules="required"
                            label=" عنوان"
                        >
                            <v-text-field
                                v-model="form.name"
                                label="عنوان"
                                single-line
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
                    <div class="col-span-4">
                        <Field
                            mode="passive"
                            name="link"
                            v-slot="{ field }"
                            rules="required"
                            label=" لینک دانلود برنامه"
                        >
                            <v-text-field
                                v-model="form.link"
                                label="لینک دانلود برنامه"
                                single-line
                                variant="solo-filled"
                                size="large"
                                v-bind="field"
                                hide-details="auto"
                            ></v-text-field>
                        </Field>
                        <div class="invalid-feedback d-block">
                            <ErrorMessage name="link" />
                        </div>
                    </div>
                    <div class="col-span-4">
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
                    <div class="col-span-12">
                        <Field
                            mode="passive"
                            name="description"
                            v-slot="{ field }"
                            rules="required"
                            label="توضیحات"
                        >
                            <v-textarea
                                v-model="form.description"
                                label="توضیحات"
                                single-line
                                variant="solo-filled"
                                size="large"
                                v-bind="field"
                                hide-details="auto"
                            ></v-textarea>
                        </Field>
                        <div class="invalid-feedback d-block">
                            <ErrorMessage name="description" />
                        </div>
                    </div>
                    <div class="col-span-12">
                        <Field
                            mode="passive"
                            name="video"
                            v-slot="{ field }"
                            label="ویدیو آموزشی"
                        >
                            <v-file-input
                                accept="video/*"
                                label="ویدیو آموزشی"
                                size="large"
                                hide-details="auto"
                                @change="handleChangeVideo"
                            ></v-file-input>
                        </Field>
                        <div class="invalid-feedback d-block">
                            <ErrorMessage name="video" />
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
        <v-snackbar absolute v-model="visible_success_message" :timeout="20000">
            برنامه با موفقیت ویرایش شد.
        </v-snackbar>
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from "vue";
import ApiService from "@/Core/services/ApiService";
import { useRoute, useRouter } from "vue-router";
import { ErrorMessage, Field, Form } from "vee-validate";

const loading = ref(false);
const formRef = ref(null);
const form = ref({
    name: null,
    link: null,
    description: null,
    status: "active",
    video: null,
});
const visible_success_message = ref(false);
const rules = ref([
    (value) => {
        if (value) return true;
        return "نام   برنامه  الزامی می باشد";
    },
]);
const statuses = ref([
    { state: "فعال", value: "active" },
    { state: "غیرفعال", value: "inactive" },
]);
const router = useRouter();
const route = useRoute();
const handleCreate = async (event) => {
    const { valid } = await formRef.value.validate();
    if (valid) {
        loading.value = true;
        const form_data = new FormData();
        form_data.append("guide_platform_id", route.params.id);
        form_data.append("name", form.value.name);
        form_data.append("link", form.value.link);
        form_data.append("status", form.value.status);
        form_data.append("description", form.value.description);
        if (form.value.video) {
            form_data.append("video", form.value.video);
        }

        const { data } = await ApiService.put(
            `/api/panel/guide/platform/${route.params.platform}/clients/${route.params.id}`,
            form_data
        );
        if (data.status == 200) {
            visible_success_message.value = true;
            router.push({
                name: "panel-platforms-clients-index",
                params: { id: route.params.platform },
            });
        }
    }
};

const handleChangeVideo = (e) => {
    form.value.video = e.target.files[0];
    formRef.value.setFieldValue("video", form.value.video);
};

const fetchData = async () => {
    let { data } = await ApiService.get(
        `/api/panel/guide/platform/${route.params.platform}/clients/${route.params.id}`
    );

    form.value.name = data.data.name;
    form.value.link = data.data.link;
    form.value.status = data.data.status;
    form.value.description = data.data.description;
    formRef.value.setValues({
        ...form.value,
    });
};

onMounted(() => {
    fetchData();
});
</script>

<style scoped></style>
