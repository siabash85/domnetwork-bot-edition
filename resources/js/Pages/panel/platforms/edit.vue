<template>
    <div>
        <v-sheet>
            <div class="mb-6">
                <h2 class="text-xl">ویرایش پلتفرم</h2>
            </div>

            <Form ref="formRef" @submit="handleUpdate">
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12">
                        <Field
                            mode="passive"
                            name="name"
                            v-slot="{ field }"
                            rules="required"
                            label=" نام پلتفرم"
                        >
                            <v-text-field
                                v-model="form.name"
                                label="نام پلتفرم"
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
            پلتفرم با موفقیت ویرایش شد.
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
});
const visible_success_message = ref(false);
const rules = ref([
    (value) => {
        if (value) return true;
        return "نام   پلتفرم  الزامی می باشد";
    },
]);

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
        form_data.append("name", form.value.name);

        const { data } = await ApiService.put(
            `/api/panel/guide/platforms/${route.params.id}`,
            form_data
        );
        if (data.status == 200) {
            visible_success_message.value = true;
            router.push({ name: "panel-platforms-index" });
        }
    }
};

const fetchData = async () => {
    let { data } = await ApiService.get(
        `/api/panel/guide/platforms/${route.params.id}`
    );
    form.value.name = data.data?.name;

    formRef.value.setValues({
        ...form.value,
    });
};

onMounted(() => {
    fetchData();
});
</script>

<style scoped></style>
