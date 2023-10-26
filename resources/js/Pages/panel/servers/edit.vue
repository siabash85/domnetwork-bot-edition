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
                        <h2 class="text-xl">ویرایش سرور</h2>
                    </div>
                    <Form ref="formRef" @submit="handleUpdate">
                        <v-text-field
                            v-model="form.name"
                            :rules="rules"
                            label="نام"
                            density="compact"
                            single-line
                            variant="solo"
                        ></v-text-field>
                        <v-radio-group v-model="form.is_active">
                            <template v-slot:label>
                                <div>وضعیت</div>
                            </template>
                            <v-radio label="فعال" value="1"></v-radio>
                            <v-radio label="غیرفعال" value="0"></v-radio>
                        </v-radio-group>

                        <v-radio-group v-model="form.is_default">
                            <template v-slot:label>
                                <div>سرور پیش فرض</div>
                            </template>
                            <v-radio label="می باشد" value="1"></v-radio>
                            <v-radio label="نمی باشد" value="0"></v-radio>
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
            سرور با موفقیت ویرایش شد.
        </v-snackbar>
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watchEffect, nextTick } from "vue";
import ApiService from "@/Core/services/ApiService";
import { useRoute, useRouter } from "vue-router";
import { BaseSkeleton, BaseSkeletonItem } from "@/Components/skeleton";
const loader = ref(true);
const loading = ref(false);
const formRef = ref(null);
import { ErrorMessage, Field, Form } from "vee-validate";
const form = ref({
    name: null,
    is_active: false,
    is_default: false,
});
const visible_success_message = ref(false);
const rules = ref([
    (value) => {
        if (value) return true;
        return "نام  سرور  الزامی می باشد";
    },
]);
const router = useRouter();
const route = useRoute();
const handleUpdate = async (event) => {
    loading.value = true;
    const form_data = new FormData();
    form_data.append("name", form.value.name);
    form_data.append("is_active", form.value.is_active);
    form_data.append("is_default", form.value.is_default);
    const { data } = await ApiService.put(
        `/api/panel/servers/${route.params.id}`,
        form_data
    );
    if (data.status == 200) {
        visible_success_message.value = true;
        router.push({ name: "panel-servers-index" });
    }
};

const fetchData = async () => {
    const { data } = await ApiService.get(
        `/api/panel/servers/${route.params.id}`
    );
    form.value.name = data.data.name;
    form.value.is_active = data.data.is_active.toString();
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
