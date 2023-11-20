<template>
    <div>
        <v-sheet>
            <div class="mb-6">
                <h2 class="text-xl">ایجاد سرور</h2>
            </div>
            <v-form
                ref="formRef"
                validate-on="submit"
                @submit.prevent="handleCreate"
            >
                <div class="grid grid-cols-12 gap-2">
                    <div class="col-span-12">
                        <v-text-field
                            v-model="form.name"
                            :rules="rules"
                            label="نام"
                            density="compact"
                            single-line
                            variant="solo"
                        ></v-text-field>
                    </div>
                    <div class="col-span-12 lg:col-span-6">
                        <v-text-field
                            v-model="form.username"
                            label="نام کاربری سرور"
                            density="compact"
                            single-line
                            variant="solo"
                        ></v-text-field>
                    </div>
                    <div class="col-span-12 lg:col-span-6">
                        <v-text-field
                            v-model="form.password"
                            label=" رمز عبور سرور"
                            density="compact"
                            single-line
                            variant="solo"
                        ></v-text-field>
                    </div>
                    <div class="col-span-12 lg:col-span-4">
                        <v-text-field
                            v-model="form.address"
                            label="آدرس سرور"
                            density="compact"
                            single-line
                            variant="solo"
                        ></v-text-field>
                    </div>
                    <div class="col-span-12 lg:col-span-4">
                        <v-text-field
                            v-model="form.inbound"
                            label="inbound id"
                            density="compact"
                            single-line
                            variant="solo"
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
                    >ایجاد
                </v-btn>
            </v-form>
        </v-sheet>
        <v-snackbar absolute v-model="visible_success_message" :timeout="20000">
            سرور با موفقیت ایجاد شد.
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
    username: null,
    password: null,
    address: null,
    inbound: null,

    is_active: "1",
    is_default: "0",
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
const handleCreate = async (event) => {
    const { valid } = await formRef.value.validate();
    if (valid) {
        loading.value = true;
        const form_data = new FormData();
        form_data.append("name", form.value.name);
        form_data.append("username", form.value.username);
        form_data.append("password", form.value.password);
        form_data.append("address", form.value.address);
        form_data.append("inbound", form.value.inbound);

        form_data.append("is_active", form.value.is_active);
        form_data.append("is_default", form.value.is_default);
        const { data } = await ApiService.post(`/api/panel/servers`, form_data);
        if (data.status == 200) {
            visible_success_message.value = true;
            router.push({ name: "panel-servers-index" });
        }
    }
};

const fetchData = async () => {
    const { data } = await ApiService.get(
        `/api/panel/servers/${route.params.id}`
    );
};

onMounted(() => {
    fetchData();
});
</script>

<style scoped></style>
