<template>
    <div>
        <v-sheet>
            <div class="mb-6">
                <h2 class="text-xl">ایجاد بازه زمانی</h2>
            </div>
            <v-form
                ref="formRef"
                validate-on="submit"
                @submit.prevent="handleCreate"
            >
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12 lg:col-span-4">
                        <v-text-field
                            type="number"
                            v-model="form.name"
                            :rules="rules"
                            label="روز به عدد"
                            density="compact"
                            single-line
                            hint="بازه زمانی را تعداد روز وارد کنید"
                        ></v-text-field>
                    </div>
                    <div class="col-span-12 lg:col-span-4">
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
                    <div class="col-span-12 lg:col-span-4">
                        <v-text-field
                            type="number"
                            v-model="form.price"
                            label="مبلغ تمدید سرویس به ازای هر گیگابایت"
                            density="compact"
                            single-line
                            persistent-hint
                            hint=" مقدار وارد شده به تومان می باشد."
                        ></v-text-field>
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
            بازه زمانی با موفقیت ایجاد شد.
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
    price: null,
});
const visible_success_message = ref(false);
const rules = ref([
    (value) => {
        if (value) return true;
        return "نام  بازه زمانی  الزامی می باشد";
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
        form_data.append("price", form.value.price);
        const { data } = await ApiService.post(
            `/api/panel/package/durations`,
            form_data
        );
        if (data.status == 200) {
            visible_success_message.value = true;
            router.push({ name: "panel-durations-index" });
        }
    }
};

const fetchData = async () => {
    const { data } = await ApiService.get(
        `/api/panel/durations/${route.params.id}`
    );
};

onMounted(() => {
    fetchData();
});
</script>

<style scoped></style>
