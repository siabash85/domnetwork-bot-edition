<template>
    <div>
        <v-sheet>
            <div class="mb-6">
                <h2 class="text-xl">ویرایش حرکت</h2>
            </div>
            <v-form
                ref="formRef"
                validate-on="submit"
                @submit.prevent="handleUpdate"
            >
                <v-text-field
                    v-model="form.name"
                    :rules="rules"
                    label="نام"
                    density="compact"
                    single-line
                    variant="solo"
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
                    >ویرایش</v-btn
                >
            </v-form>
        </v-sheet>
        <v-snackbar absolute v-model="visible_success_message" :timeout="20000">
            حرکت با موفقیت ویرایش شد.
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
    is_aerobic: 0,
    is_repeater: 0,
});
const visible_success_message = ref(false);
const rules = ref([
    (value) => {
        if (value) return true;
        return "نام  حرکت  الزامی می باشد";
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
        form_data.append("is_aerobic", form.value.is_aerobic);
        form_data.append("is_repeater", form.value.is_repeater);
        const { data } = await ApiService.put(
            `/api/panel/movements/${route.params.id}`,
            form_data
        );
        if (data.success) {
            visible_success_message.value = true;
            router.push({ name: "panel-movements-index" });
        }
    }
};

const fetchData = async () => {
    const { data } = await ApiService.get(
        `/api/panel/movements/${route.params.id}`
    );
    form.value.name = data.data.name;
    form.value.is_aerobic = data.data.is_aerobic.toString();
    form.value.is_repeater = data.data.is_repeater.toString();
};

onMounted(() => {
    fetchData();
});
</script>

<style scoped></style>
