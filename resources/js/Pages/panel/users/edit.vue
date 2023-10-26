<template>
    <div>
        <v-sheet>
            <div class="mb-6">
                <h2 class="text-xl">مشاهده پیام</h2>
            </div>

            <Form ref="formRef" @submit="handleUpdate">
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12">
                        <Field
                            mode="passive"
                            name="message"
                            v-slot="{ field }"
                            rules="required"
                            label="پیام کاربر"
                        >
                            <v-textarea
                                v-model="form.message"
                                label="پیام کاربر"
                                variant="solo-filled"
                                size="large"
                                v-bind="field"
                                hide-details="auto"
                            ></v-textarea>
                        </Field>
                        <div class="invalid-feedback d-block">
                            <ErrorMessage name="message" />
                        </div>
                    </div>
                    <div class="col-span-12">
                        <Field
                            mode="passive"
                            name="answer"
                            v-slot="{ field }"
                            label="پاسخ"
                        >
                            <v-textarea
                                v-model="form.answer"
                                label="پاسخ"
                                variant="solo-filled"
                                size="large"
                                v-bind="field"
                                hide-details="auto"
                            ></v-textarea>
                        </Field>
                        <div class="invalid-feedback d-block">
                            <ErrorMessage name="answer" />
                        </div>
                    </div>
                </div>

                <v-btn
                    :loading="loading"
                    color="light-blue-accent-4"
                    type="submit"
                    block
                    class="mt-2"
                    >پاسخ</v-btn
                >
            </Form>
        </v-sheet>
        <v-snackbar absolute v-model="visible_success_message" :timeout="20000">
            پیام با موفقیت ارسال شد.
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
    message: null,
    answer: null,
});
const visible_success_message = ref(false);
const rules = ref([
    (value) => {
        if (value) return true;
        return "نام     الزامی می باشد";
    },
]);

const router = useRouter();
const route = useRoute();
const handleUpdate = async (event) => {
    const { valid } = await formRef.value.validate();
    if (valid) {
        loading.value = true;
        const form_data = new FormData();
        form_data.append("answer", form.value.answer);

        const { data } = await ApiService.put(
            `/api/panel/support/messages/${route.params.id}`,
            form_data
        );
        if (data.status == 200) {
            visible_success_message.value = true;
            router.push({ name: "panel-messages-index" });
        }
    }
};

const fetchData = async () => {
    let { data } = await ApiService.get(
        `/api/panel/support/messages/${route.params.id}`
    );
    form.value.message = data.data?.message;
    form.value.answer = data.data?.answer;
    formRef.value.setValues({
        ...form.value,
    });
};

onMounted(() => {
    fetchData();
});
</script>

<style scoped></style>
