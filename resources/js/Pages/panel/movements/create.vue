<template>
    <div>
        <v-sheet>
            <div class="mb-6">
                <h2 class="text-xl">ایجاد حرکت</h2>
            </div>
            <v-form
                ref="formRef"
                validate-on="submit"
                @submit.prevent="handleCreate"
            >
                <v-text-field
                    v-model="form.name"
                    :rules="rules"
                    label="نام"
                    density="compact"
                    single-line
                    variant="solo"
                    class="mb-4"
                ></v-text-field>

                <v-radio-group v-model="form.is_aerobic">
                    <template v-slot:label>
                        <div>هوازی</div>
                    </template>
                    <v-radio label="هوازی" value="1"></v-radio>
                    <v-radio label="غیرهوازی" value="0"></v-radio>
                </v-radio-group>

                <v-radio-group v-model="form.is_repeater">
                    <template v-slot:label>
                        <div>تکرار شونده</div>
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
                    >ایجاد</v-btn
                >
            </v-form>
        </v-sheet>
    </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import ApiService from "@/Core/services/ApiService";
import { useRouter } from "vue-router";

const loading = ref(false);
const formRef = ref(null);
const form = ref({
    name: null,
    is_aerobic: 0,
    is_repeater: 0,
});
const rules = ref([
    (value) => {
        if (value) return true;
        return "نام حرکت   الزامی می باشد";
    },
]);
const router = useRouter();

const handleCreate = async (event) => {
    const { valid } = await formRef.value.validate();
    if (valid) {
        loading.value = true;
        const form_data = new FormData();
        form_data.append("name", form.value.name);
        form_data.append("is_aerobic", form.value.is_aerobic);
        form_data.append("is_repeater", form.value.is_repeater);
        const { data } = await ApiService.post(
            "/api/panel/movements",
            form_data
        );

        if (data.success) {
            router.push({ name: "panel-movements-index" });
        }
    }
};
</script>

<style scoped></style>
