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
                        <h2 class="text-xl">ویرایش پکیج</h2>
                    </div>
                    <Form ref="formRef" @submit="handleUpdate">
                        <div class="grid grid-cols-12 gap-4">
                            <div class="col-span-12 lg:col-span-6">
                                <v-text-field
                                    v-model="form.name"
                                    :rules="rules"
                                    label="نام"
                                    density="compact"
                                    single-line
                                ></v-text-field>
                            </div>
                            <div class="col-span-12 lg:col-span-6">
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
                        </div>

                        <v-radio-group v-model="form.is_active">
                            <template v-slot:label>
                                <div>وضعیت</div>
                            </template>
                            <v-radio label="فعال" value="1"></v-radio>
                            <v-radio label="غیرفعال" value="0"></v-radio>
                        </v-radio-group>

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
            پکیج با موفقیت ویرایش شد.
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
    is_active: "active",
});
const visible_success_message = ref(false);
const rules = ref([
    (value) => {
        if (value) return true;
        return "نام   پکیج  الزامی می باشد";
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
        form_data.append("is_active", form.value.is_active);
        const { data } = await ApiService.put(
            `/api/panel/packages/${route.params.id}`,
            form_data
        );
        if (data.status == 200) {
            visible_success_message.value = true;
            router.push({ name: "panel-packages-index" });
        }
    }
};

const fetchData = async () => {
    const { data } = await ApiService.get(
        `/api/panel/packages/${route.params.id}`
    );
    form.value.name = data.data.name;
    form.value.value = data.data.value;
    form.value.is_active = data.data.is_active.toString();

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
