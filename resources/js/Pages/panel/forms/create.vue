<template>
    <div>
        <v-sheet>
            <div class="mb-6">
                <h2 class="text-xl">ایجاد برنامه</h2>
            </div>

            <Form ref="formRef" @submit="handleCreate">
                <div class="grid grid-cols-12 gap-2">
                    <div class="col-span-12 lg:col-span-6">
                        <Field
                            mode="passive"
                            name="first_name"
                            v-slot="{ field }"
                            rules="required"
                            label="نام"
                        >
                            <v-text-field
                                v-bind="field"
                                v-model="form.first_name"
                                label="نام"
                                density="compact"
                                single-line
                                hide-details="auto"
                            ></v-text-field>
                        </Field>
                        <div class="invalid-feedback d-block">
                            <ErrorMessage name="first_name" />
                        </div>
                    </div>
                    <div class="col-span-12 lg:col-span-6">
                        <Field
                            mode="passive"
                            name="last_name"
                            v-slot="{ field }"
                            rules="required"
                            label="نام خانوادگی"
                        >
                            <v-text-field
                                v-model="form.last_name"
                                label="نام خانوادگی"
                                density="compact"
                                single-line
                                v-bind="field"
                                hide-details="auto"
                            ></v-text-field>
                        </Field>
                        <div class="invalid-feedback d-block">
                            <ErrorMessage name="last_name" />
                        </div>
                    </div>
                    <div class="col-span-12 lg:col-span-3">
                        <Field
                            mode="passive"
                            name="mobile"
                            v-slot="{ field }"
                            rules="required"
                            label="شماره همراه"
                        >
                            <v-text-field
                                v-model="form.mobile"
                                label="شماره همراه"
                                density="compact"
                                single-line
                                type="number"
                                v-bind="field"
                                hide-details="auto"
                            ></v-text-field>
                        </Field>
                        <div class="invalid-feedback d-block">
                            <ErrorMessage name="mobile" />
                        </div>
                    </div>
                    <div class="col-span-12 lg:col-span-3">
                        <Field
                            mode="passive"
                            name="height"
                            v-slot="{ field }"
                            rules="required"
                            label=" قد"
                        >
                            <v-text-field
                                v-model="form.height"
                                label=" قد"
                                density="compact"
                                single-line
                                type="number"
                                v-bind="field"
                                hide-details="auto"
                            ></v-text-field>
                        </Field>
                        <div class="invalid-feedback d-block">
                            <ErrorMessage name="height" />
                        </div>
                    </div>
                    <div class="col-span-12 lg:col-span-3">
                        <Field
                            mode="passive"
                            name="weight"
                            v-slot="{ field }"
                            rules="required"
                            label=" وزن"
                        >
                            <v-text-field
                                v-model="form.weight"
                                label=" وزن"
                                density="compact"
                                single-line
                                type="number"
                                v-bind="field"
                                hide-details="auto"
                            ></v-text-field>
                        </Field>
                        <div class="invalid-feedback d-block">
                            <ErrorMessage name="weight" />
                        </div>
                    </div>
                    <div class="col-span-12 lg:col-span-3">
                        <Field
                            mode="passive"
                            name="age"
                            v-slot="{ field }"
                            rules="required"
                            label="سن"
                        >
                            <v-text-field
                                v-model="form.age"
                                label="سن"
                                density="compact"
                                single-line
                                type="number"
                                v-bind="field"
                                hide-details="auto"
                            ></v-text-field>
                        </Field>
                        <div class="invalid-feedback d-block">
                            <ErrorMessage name="age" />
                        </div>
                    </div>

                    <div class="col-span-12 lg:col-span-12">
                        <Field
                            mode="passive"
                            name="sports_history"
                            v-slot="{ field }"
                            rules="required"
                            label="سابقه ورزشی"
                        >
                            <v-text-field
                                v-model="form.sports_history"
                                label="سابقه ورزشی"
                                density="compact"
                                single-line
                                v-bind="field"
                                hide-details="auto"
                            ></v-text-field>
                        </Field>
                        <div class="invalid-feedback d-block">
                            <ErrorMessage name="sports_history" />
                        </div>
                    </div>
                    <div class="col-span-12 lg:col-span-12">
                        <Field
                            mode="passive"
                            name="illness"
                            v-slot="{ field }"
                            rules="required"
                            label="نوع آسیب یا بیماری"
                        >
                            <v-text-field
                                v-model="form.illness"
                                label="نوع آسیب یا بیماری"
                                density="compact"
                                single-line
                                v-bind="field"
                                hide-details="auto"
                            ></v-text-field>
                        </Field>
                        <div class="invalid-feedback d-block">
                            <ErrorMessage name="illness" />
                        </div>
                    </div>
                    <div class="col-span-12 lg:col-span-12">
                        <Field
                            mode="passive"
                            name="purpose"
                            v-slot="{ field }"
                            rules="required"
                            label="هدف تمرین"
                        >
                            <v-text-field
                                v-model="form.purpose"
                                label="هدف تمرین"
                                density="compact"
                                single-line
                                v-bind="field"
                                hide-details="auto"
                            ></v-text-field>
                        </Field>
                        <div class="invalid-feedback d-block">
                            <ErrorMessage name="purpose" />
                        </div>
                    </div>
                    <div class="col-span-12 lg:col-span-6">
                        <Field
                            mode="passive"
                            name="use_duration"
                            v-slot="{ field }"
                            rules="required"
                            label="مدت زمان استفاده از برنامه"
                        >
                            <v-text-field
                                v-model="form.use_duration"
                                label="مدت زمان استفاده از برنامه"
                                density="compact"
                                single-line
                                v-bind="field"
                                hide-details="auto"
                            ></v-text-field>
                        </Field>
                        <div class="invalid-feedback d-block">
                            <ErrorMessage name="use_duration" />
                        </div>
                    </div>
                    <div class="col-span-12 lg:col-span-6">
                        <Field
                            mode="passive"
                            name="modified_date"
                            v-slot="{ field }"
                            rules="required"
                            label="تاریخ صدور برنامه"
                        >
                            <date-picker
                                v-bind="field"
                                placeholder="تاریخ صدور برنامه"
                                v-model="form.modified_date"
                            >
                            </date-picker>
                        </Field>
                        <div class="invalid-feedback d-block">
                            <ErrorMessage name="modified_date" />
                        </div>
                    </div>
                    <div class="col-span-12">
                        <v-btn
                            :loading="loading"
                            color="light-blue-accent-4"
                            type="submit"
                            block
                            class="mt-2"
                            >ایجاد</v-btn
                        >
                    </div>
                </div>
            </Form>
        </v-sheet>
    </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import ApiService from "@/Core/services/ApiService";
import { useRouter } from "vue-router";
import DatePicker from "vue3-persian-datetime-picker";
import { ErrorMessage, Field, Form } from "vee-validate";

const loading = ref(false);
const formRef = ref(null);
const form = ref({
    first_name: null,
    last_name: null,
    mobile: null,
    height: null,
    weight: null,
    age: null,
    sports_history: null,
    modified_date: "",
    illness: null,
    purpose: null,
    use_duration: null,
});

const router = useRouter();

const handleCreate = async (event) => {
    const { valid } = await formRef.value.validate();
    if (valid) {
        loading.value = true;
        const form_data = new FormData();

        form_data.append("first_name", form.value.first_name);
        form_data.append("last_name", form.value.last_name);
        form_data.append("mobile", form.value.mobile);
        form_data.append("height", form.value.height);
        form_data.append("weight", form.value.weight);
        form_data.append("age", form.value.age);
        form_data.append("sports_history", form.value.sports_history);
        form_data.append("modified_date", form.value.modified_date);
        form_data.append("illness", form.value.illness);
        form_data.append("purpose", form.value.purpose);
        form_data.append("use_duration", form.value.use_duration);

        const { data } = await ApiService.post("/api/panel/forms", form_data);

        if (data.success) {
            router.push({ name: "panel-forms-index" });
        }
    }
};
</script>

<style scoped></style>
