<template>
    <div>
        <v-sheet>
            <div class="mb-6">
                <h2 class="text-xl">ایجاد اشتراک</h2>
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
                        <Field
                            mode="passive"
                            name="user_id"
                            v-slot="{ field }"
                            rules="required"
                            label="کاربر"
                        >
                            <v-select
                                v-bind="field"
                                v-model="form.user_id"
                                label="انتخاب  کاربر"
                                :items="users"
                                item-title="username"
                                item-value="id"
                                single-line
                                variant="solo-filled"
                                hide-details="auto"
                            ></v-select>
                        </Field>
                        <div class="invalid-feedback d-block">
                            <ErrorMessage name="user_id" />
                        </div>
                    </div>
                    <div class="col-span-12 lg:col-span-6">
                        <Field
                            mode="passive"
                            name="name"
                            v-slot="{ field }"
                            rules="required"
                            label=" نام اشتراک"
                        >
                            <v-text-field
                                v-model="form.name"
                                label="نام اشتراک"
                                single-line
                                variant="solo-filled"
                                size="large"
                                v-bind="field"
                                hide-details="auto"
                            ></v-text-field>
                        </Field>
                        <div class="invalid-feedback d-block">
                            <ErrorMessage name="name" />
                        </div>
                    </div>
                    <!-- <div class="col-span-12 lg:col-span-4">
                        <Field
                            mode="passive"
                            name="expire_date"
                            v-slot="{ field }"
                            rules="required"
                            label=" نام اشتراک"
                        >
                            <date-picker
                                v-bind="field"
                                placeholder="تاریخ  انقضا"
                                v-model="form.expire_date"
                            >
                            </date-picker>
                        </Field>
                        <div class="invalid-feedback d-block">
                            <ErrorMessage name="expire_date" />
                        </div>
                    </div> -->
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
            اشتراک با موفقیت ایجاد شد.
        </v-snackbar>
        <v-snackbar absolute v-model="visible_snackbar" :timeout="2000">
            اشتراک با موفقیت کپی شد.
        </v-snackbar>
        <v-snackbar
            location="top"
            class="mt-5"
            color="red"
            absolute
            v-model="wallet_message"
            :timeout="3000"
        >
            موجودی کاربر برای ایجاد این اشتراک کافی نمی باشد.
        </v-snackbar>
        <v-dialog v-model="visible_config_dialog" persistent>
            <v-card>
                <v-card-title class=""> </v-card-title>
                <v-card-text>
                    <div class="grid grid-cols-12 gap-2">
                        <div class="col-span-12">
                            <div class="mb-2">
                                <h2 class="text-xl">لینک اشتراک</h2>
                            </div>

                            <div>
                                <v-chip
                                    @click="copyToClipboard"
                                    class="text-ellipsis overflow-hidden whitespace-nowrap"
                                    color="indigo"
                                    prepend-icon="mdi-content-copy"
                                >
                                    {{ sub_link }}
                                </v-chip>
                            </div>
                        </div>
                        <div class="col-span-12">
                            <div class="mb-2">
                                <h2 class="text-xl">لینک مستقیم v2ray</h2>
                            </div>

                            <div>
                                <v-chip
                                    @click="copyConfigLink"
                                    class="text-ellipsis overflow-hidden whitespace-nowrap"
                                    prepend-icon="mdi-content-copy"
                                >
                                    {{ config_link }}
                                </v-chip>
                            </div>
                        </div>
                    </div>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>

                    <v-btn @click="handleRedirect"> تایید </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watch } from "vue";
import ApiService from "@/Core/services/ApiService";
import { useRoute, useRouter } from "vue-router";
import { ErrorMessage, Field, Form } from "vee-validate";
import DatePicker from "vue3-persian-datetime-picker";
import { useClipboard } from "@vueuse/core";
const page_path = ref(null);
const { text, copy, copied, isSupported } = useClipboard({ page_path });
const loading = ref(false);
const formRef = ref(null);
const visible_config_dialog = ref(false);
const wallet_message = ref(false);
const form = ref({
    server_id: null,
    package_duration_id: null,
    package_id: null,
    user_id: null,
    name: null,
    expire_date: null,
});
const sub_link = ref("");
const config_link = ref("");
const visible_success_message = ref(false);

const users = ref([]);
const servers = ref([]);
const durations = ref([]);
const packages = ref([]);

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
        form_data.append("user_id", form.value.user_id);
        form_data.append("name", form.value.name);
        // form_data.append("expire_date", form.value.expire_date);
        const { data } = await ApiService.post(
            `/api/panel/subscriptions`,
            form_data
        );
        if (data.status == 200) {
            visible_success_message.value = true;

            sub_link.value = data.data.sub;
            config_link.value = data.data.link;
            visible_config_dialog.value = true;
        } else {
            loading.value = false;
            wallet_message.value = true;
        }
    }
};
const handleRedirect = () => {
    router.push({ name: "panel-subscriptions-index" });
};
const visible_snackbar = ref(false);
const copyToClipboard = () => {
    copy(sub_link.value);
};

const copyConfigLink = () => {
    copy(config_link.value);
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

    let { data: users_res } = await ApiService.get(
        `/api/panel/user/select/search`
    );
    users.value = users_res.data;
};

watch(
    () => copied.value,
    (val) => {
        if (val) {
            visible_snackbar.value = true;
        }
    }
);

onMounted(() => {
    fetchData();
});
</script>

<style scoped></style>
