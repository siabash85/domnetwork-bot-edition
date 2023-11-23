<template>
    <div>
        <v-sheet>
            <div class="mb-6">
                <h2 class="text-xl">ایجاد سرویس</h2>
            </div>

            <Form ref="formRef" @submit="handleCreate">
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12 lg:col-span-4">
                        <Field
                            mode="passive"
                            name="server_id"
                            v-slot="{ field }"
                            rules="required"
                            label="سرور"
                        >
                            <v-select
                                v-bind="field"
                                v-model="form.server_id"
                                label="انتخاب  سرور"
                                :items="servers"
                                item-title="name"
                                item-value="id"
                                single-line
                                variant="solo-filled"
                                hide-details="auto"
                            ></v-select>
                        </Field>
                        <div class="invalid-feedback d-block">
                            <ErrorMessage name="server_id" />
                        </div>
                    </div>
                    <div class="col-span-12 lg:col-span-4">
                        <Field
                            mode="passive"
                            name="package_duration_id"
                            v-slot="{ field }"
                            rules="required"
                            label="بازه زمانی"
                        >
                            <v-select
                                v-bind="field"
                                v-model="form.package_duration_id"
                                label="انتخاب  بازه زمانی"
                                :items="durations"
                                item-title="name"
                                item-value="id"
                                single-line
                                variant="solo-filled"
                                hide-details="auto"
                            ></v-select>
                        </Field>
                        <div class="invalid-feedback d-block">
                            <ErrorMessage name="package_duration_id" />
                        </div>
                    </div>
                    <div class="col-span-12 lg:col-span-4">
                        <Field
                            mode="passive"
                            name="package_id"
                            v-slot="{ field }"
                            rules="required"
                            label="بازه پکیج"
                        >
                            <v-select
                                v-model="form.package_id"
                                label="انتخاب  پکیج"
                                :items="packages"
                                item-title="name"
                                item-value="id"
                                single-line
                                variant="solo-filled"
                                v-bind="field"
                                hide-details="auto"
                            ></v-select>
                        </Field>
                        <div class="invalid-feedback d-block">
                            <ErrorMessage name="package_id" />
                        </div>
                    </div>
                    <div class="col-span-12 lg:col-span-6">
                        <v-select
                            v-model="form.status"
                            label="انتخاب  وضعیت"
                            :items="statuses"
                            item-title="state"
                            item-value="value"
                            single-line
                            variant="solo-filled"
                        ></v-select>
                    </div>
                    <div class="col-span-12 lg:col-span-6">
                        <Field
                            mode="passive"
                            name="price"
                            v-slot="{ field }"
                            rules="required"
                            label=" قیمت"
                        >
                            <v-text-field
                                type="number"
                                v-model="form.price"
                                label="قیمت"
                                single-line
                                variant="solo-filled"
                                size="large"
                                v-bind="field"
                                hide-details="auto"
                            ></v-text-field>
                        </Field>
                        <div class="invalid-feedback d-block">
                            <ErrorMessage name="price" />
                        </div>
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
            </Form>
        </v-sheet>
        <v-snackbar absolute v-model="visible_success_message" :timeout="20000">
            سرویس با موفقیت ایجاد شد.
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
    server_id: null,
    package_duration_id: null,
    package_id: null,
    status: "active",
    price: null,
});
const visible_success_message = ref(false);
const rules = ref([
    (value) => {
        if (value) return true;
        return "نام   سرویس  الزامی می باشد";
    },
]);
const servers = ref([]);
const durations = ref([]);
const packages = ref([]);
const statuses = ref([
    { state: "فعال", value: "active" },
    { state: "غیرفعال", value: "inactive" },
]);
const router = useRouter();
const route = useRoute();
const handleCreate = async (event) => {
    const { valid } = await formRef.value.validate();
    if (valid) {
        loading.value = true;
        const form_data = new FormData();
        form_data.append("server_id", form.value.server_id);
        form_data.append("package_duration_id", form.value.package_duration_id);
        form_data.append("package_id", form.value.package_id);
        form_data.append("status", form.value.status);
        form_data.append("price", form.value.price);

        const { data } = await ApiService.post(
            `/api/panel/services`,
            form_data
        );
        if (data.status == 200) {
            visible_success_message.value = true;
            router.push({ name: "panel-services-index" });
        }
    }
};

const fetchData = async () => {
    let { data: servers_res } = await ApiService.get(`/api/panel/servers`);
    servers.value = servers_res.data;
    let { data: durations_res } = await ApiService.get(
        `/api/panel/package/durations`
    );
    durations.value = durations_res.data;
    let { data: packages_res } = await ApiService.get(`/api/panel/packages`);
    packages.value = packages_res.data;
};

onMounted(() => {
    fetchData();
});
</script>

<style scoped></style>
