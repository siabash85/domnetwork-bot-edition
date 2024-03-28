<template>
    <div>
        <v-sheet>
            <div class="mb-6">
                <h2 class="text-xl">ایجاد پکیج</h2>
            </div>
            <v-form
                ref="formRef"
                validate-on="submit"
                @submit.prevent="handleCreate"
            >
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-6">
                        <v-text-field
                            v-model="form.name"
                            :rules="rules"
                            label="نام"
                            density="compact"
                            single-line
                        ></v-text-field>
                    </div>

                    <div class="col-span-6">
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
                    <div class="col-span-12">
                        <v-radio-group v-model="form.is_active">
                            <template v-slot:label>
                                <div>وضعیت</div>
                            </template>
                            <v-radio label="فعال" value="1"></v-radio>
                            <v-radio label="غیرفعال" value="0"></v-radio>
                        </v-radio-group>
                    </div>
                </div>

                <v-btn
                    :loading="loading"
                    color="light-blue-accent-4"
                    type="submit"
                    block
                    class="mt-2"
                    >ایجاد</v-btn
                >
            </v-form>
        </v-sheet>
        <v-snackbar absolute v-model="visible_success_message" :timeout="20000">
            پکیج با موفقیت ایجاد شد.
        </v-snackbar>
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from "vue";
import ApiService from "@/Core/services/ApiService";
import { useRoute, useRouter } from "vue-router";

const loading = ref(false);
const formRef = ref(null);
const form = ref({
    name: null,
    value: 0,
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
const handleCreate = async (event) => {
    const { valid } = await formRef.value.validate();
    if (valid) {
        loading.value = true;
        const form_data = new FormData();
        form_data.append("name", form.value.name);
        form_data.append("value", form.value.value);
        form_data.append("is_active", form.value.is_active);
        const { data } = await ApiService.post(
            `/api/panel/packages`,
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
};

onMounted(() => {
    fetchData();
});
</script>

<style scoped></style>
