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
                        <div class="grid grid-cols-12 gap-2">
                            <div class="col-span-12 lg:col-span-4">
                                <v-text-field
                                    v-model="form.name"
                                    :rules="rules"
                                    label="نام"
                                    density="compact"
                                    single-line
                                ></v-text-field>
                            </div>
                            <div class="col-span-12 lg:col-span-4">
                                <v-text-field
                                    v-model="form.username"
                                    label="نام کاربری سرور"
                                    density="compact"
                                    single-line
                                ></v-text-field>
                            </div>
                            <div class="col-span-12 lg:col-span-4">
                                <v-text-field
                                    v-model="form.password"
                                    label=" رمز عبور سرور"
                                    density="compact"
                                    single-line
                                ></v-text-field>
                            </div>
                            <div class="col-span-12 lg:col-span-4">
                                <v-text-field
                                    v-model="form.address"
                                    label="آدرس سرور"
                                    density="compact"
                                    single-line
                                ></v-text-field>
                            </div>
                            <div class="col-span-12 lg:col-span-4">
                                <v-text-field
                                    v-model="form.inbound"
                                    label="inbound id"
                                    density="compact"
                                    single-line
                                ></v-text-field>
                            </div>
                            <div class="col-span-12 lg:col-span-4">
                                <v-select
                                    v-model="form.type"
                                    label="نوع سرور"
                                    :items="server_types"
                                    item-title="state"
                                    item-value="value"
                                    single-line
                                    density="compact"
                                ></v-select>
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
    username: null,
    password: null,
    address: null,
    inbound: null,
    is_active: false,
    is_default: false,
    type: "sanaei",
});

const server_types = ref([
    { state: "سنایی", value: "sanaei" },
    { state: "مرزبان", value: "marzban" },
]);

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
    form_data.append("username", form.value.username);
    form_data.append("password", form.value.password);
    form_data.append("address", form.value.address);
    form_data.append("inbound", form.value.inbound);
    form_data.append("is_active", form.value.is_active);
    form_data.append("is_default", form.value.is_default);
    form_data.append("type", form.value.type);

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
    form.value.username = data.data.username;
    form.value.password = data.data.password;
    form.value.address = data.data.address;
    form.value.inbound = data.data.inbound;
    form.value.is_active = data.data.is_active ? "1" : "0";
    form.value.is_default = data.data.is_default ? "1" : "0";
    loader.value = false;
    form.value.type = data.data.type;
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
