<template>
    <div>
        <base-skeleton animated :loading="loading">
            <template #template>
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12 lg:col-span-12">
                        <base-skeleton-item
                            variant="card"
                            class="h-[300px]"
                        ></base-skeleton-item>
                    </div>
                </div>
            </template>
            <template #default>
                <div class="flex justify-between mb-12 items-center">
                    <h2 class="text-xl">لیست اشتراک ها</h2>
                    <v-btn
                        :to="{ name: 'panel-subscriptions-create' }"
                        color="blue-accent-2"
                    >
                        ایجاد اشتراک
                    </v-btn>
                </div>

                <v-card flat title="">
                    <template v-slot:text>
                        <v-text-field
                            v-model="search"
                            label="جستجو"
                            prepend-inner-icon="mdi-magnify"
                            single-line
                            variant="outlined"
                            hide-details
                        ></v-text-field>
                    </template>

                    <v-data-table
                        no-data-text="نتیجه ای یافت نشد"
                        page-text=""
                        items-per-page-text=""
                        :search="search"
                        :items-per-page="10"
                        :items="subscriptions"
                        :headers="headers"
                        :items-per-page-options="[
                            { value: 5, title: '5' },
                            { value: 10, title: '10' },
                            { value: 25, title: '25' },
                            { value: 50, title: '50' },
                            { value: 100, title: '100' },
                        ]"
                    >
                        <template v-slot:header.username>
                            <div class="whitespace-nowrap">کاربر</div>
                        </template>
                        <template v-slot:header.name>
                            <div class="whitespace-nowrap">نام سرویس</div>
                        </template>

                        <template v-slot:header.code>
                            <div class="whitespace-nowrap">کد سرویس</div>
                        </template>
                        <template v-slot:header.slug>
                            <div class="whitespace-nowrap">شناسه سرویس</div>
                        </template>
                        <template v-slot:header.service>
                            <div class="whitespace-nowrap">سرور</div>
                        </template>
                        <template v-slot:header.service.package_duration>
                            <div class="whitespace-nowrap">زمان سرویس</div>
                        </template>
                        <template v-slot:header.service.package>
                            <div class="whitespace-nowrap">پکیج</div>
                        </template>
                        <template v-slot:header.status>
                            <div class="whitespace-nowrap">وضعیت</div>
                        </template>
                        <template v-slot:header.created_at>
                            <div class="whitespace-nowrap">تاریخ ایجاد</div>
                        </template>
                        <template v-slot:header.expire_at>
                            <div class="whitespace-nowrap">تاریخ پایان</div>
                        </template>
                        <template v-slot:header.actions>
                            <div class="whitespace-nowrap">عملیات</div>
                        </template>

                        <template v-slot:item.username="{ item }">
                            <div class="whitespace-nowrap">
                                {{ item.user?.username }} -
                                {{ item.user?.uid }}
                            </div>
                        </template>
                        <template v-slot:item.name="{ item }">
                            <div class="whitespace-nowrap">
                                {{ item.name }}
                            </div>
                        </template>
                        <template v-slot:item.code="{ item }">
                            <div class="whitespace-nowrap">
                                {{ item.code }}
                            </div>
                        </template>
                        <template v-slot:item.slug="{ item }">
                            <div class="whitespace-nowrap">
                                {{ item.slug }}
                            </div>
                        </template>
                        <template v-slot:item.service="{ item }">
                            <div class="whitespace-nowrap">
                                {{ item.service?.server?.name }}
                            </div>
                        </template>
                        <template
                            v-slot:item.service.package_duration="{ item }"
                        >
                            <div class="whitespace-nowrap">
                                {{ item.service?.package_duration?.value }}
                                روز
                            </div>
                        </template>
                        <template v-slot:item.service.package="{ item }">
                            <div class="whitespace-nowrap">
                                {{ item.service?.package?.name }}
                            </div>
                        </template>
                        <template v-slot:item.status="{ item }">
                            <div class="whitespace-nowrap">
                                <template v-if="item.status == 'active'">
                                    <v-chip color="green" text-color="white">
                                        فعال
                                    </v-chip>
                                </template>
                                <template v-if="item.status == 'inactive'">
                                    <v-chip color="red" text-color="white">
                                        غیرفعال
                                    </v-chip>
                                </template>
                            </div>
                        </template>
                        <template v-slot:item.created_at="{ item }">
                            <div class="whitespace-nowrap">
                                {{ item.created_at }}
                            </div>
                        </template>
                        <template v-slot:item.expire_at="{ item }">
                            <div class="whitespace-nowrap">
                                {{ item.expire_at }}
                            </div>
                        </template>

                        <template v-slot:item.actions="{ item }">
                            <div class="flex items-center">
                                <v-btn
                                    @click="handleShowExtensionDialog(item)"
                                    prepend-icon="mdi-timer-plus-outline"
                                >
                                    تمدید
                                </v-btn>
                                <v-btn
                                    :to="{
                                        name: 'panel-subscriptions-edit',
                                        params: { id: item.id },
                                    }"
                                    prepend-icon="mdi-pencil-box-outline"
                                    class="mr-2"
                                >
                                    مشاهده
                                </v-btn>
                            </div>
                        </template>
                    </v-data-table>
                </v-card>
            </template>
        </base-skeleton>

        <v-dialog v-model="visible_extension_dialog" persistent width="350px">
            <v-card>
                <v-card-title class=""> تمدید اشتراک کاربر </v-card-title>
                <Form ref="formRef" @submit="handleCreate">
                    <v-card-text>
                        <div class="grid grid-cols-12 gap-4">
                            <div class="col-span-12 lg:col-span-12">
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
                                        label="انتخاب  بازه زمانی (روز)"
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
                            <div class="col-span-12 lg:col-span-12">
                                <Field
                                    mode="passive"
                                    name="volume"
                                    v-slot="{ field }"
                                    rules="required"
                                    label=" حجم به گیگ"
                                >
                                    <v-text-field
                                        type="number"
                                        v-model="form.volume"
                                        label="حجم به گیگ"
                                        single-line
                                        variant="solo-filled"
                                        size="large"
                                        v-bind="field"
                                        hide-details="auto"
                                    ></v-text-field>
                                </Field>
                                <div class="invalid-feedback d-block">
                                    <ErrorMessage name="volume" />
                                </div>
                            </div>
                        </div>
                    </v-card-text>
                    <v-card-actions class="flex justify-end">
                        <v-btn
                            color="green-darken-1"
                            variant="text"
                            @click="visible_extension_dialog = false"
                        >
                            لغو
                        </v-btn>
                        <v-btn
                            :loading="loader"
                            color="green-darken-1"
                            type="submit"
                        >
                            تایید
                        </v-btn>
                    </v-card-actions>
                </Form>
            </v-card>
        </v-dialog>

        <v-snackbar
            absolute
            v-model="visible_success_message"
            :timeout="3000"
            class="mb-5"
        >
            اشتراک با موفقیت تمدید شد.
        </v-snackbar>
        <v-snackbar
            location="top"
            class="mt-5"
            color="red"
            absolute
            v-model="wallet_message"
            :timeout="3000"
        >
            موجودی کاربر برای تمدید این سرویس کافی نمی باشد.
        </v-snackbar>
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watchEffect } from "vue";
import ApiService from "@/Core/services/ApiService";
const visible_extension_dialog = ref(false);
const visible_delete_message = ref(false);
import { BaseSkeleton, BaseSkeletonItem } from "@/Components/skeleton";
import { ErrorMessage, Field, Form } from "vee-validate";
const loading = ref(true);
const loader = ref(false);
const search = ref("");
const headers = ref([
    { key: "username", title: "نام کاربری", sortable: true },
    { key: "name", title: "نام سرویس", sortable: true },
    { key: "code", title: "کد سرویس", sortable: true },
    { key: "slug", title: "شناسه سرویس", sortable: true },
    { key: "service", title: "سرور", sortable: true },
    { key: "service.package_duration", title: "زمان سرویس", sortable: true },
    { key: "service.package", title: "پکیج", sortable: true },
    { key: "status", title: "وضعیت", sortable: true },
    { key: "created_at", title: "تاریخ ایجاد", sortable: true },
    { key: "expire_at", title: "تاریخ پایان", sortable: true },
    { key: "actions", title: "عملیات", sortable: true },
]);
const form = ref({
    package_duration_id: null,
    volume: "",
});
const durations = ref([]);
const visible_success_message = ref(false);
const wallet_message = ref(false);

const selected_subscription = ref(null);
const formRef = ref(null);
const subscriptions = ref([]);

const fetchData = async () => {
    const { data } = await ApiService.get("api/panel/subscriptions");
    subscriptions.value = data.data;

    let { data: durations_res } = await ApiService.get(
        `/api/panel/package/durations`
    );
    durations.value = durations_res.data;
    loading.value = false;
};
const handleShowExtensionDialog = (item) => {
    visible_extension_dialog.value = true;
    selected_subscription.value = item;
};

const handleCreate = async (event) => {
    loader.value = true;
    const form_data = new FormData();
    form_data.append("subscription_id", selected_subscription.value.id);
    form_data.append("package_duration_id", form.value.package_duration_id);
    form_data.append("volume", form.value.volume);
    const { data } = await ApiService.post(
        `/api/panel/subscription/extension`,
        form_data
    );
    if (data.status == 200) {
        visible_success_message.value = true;
        visible_extension_dialog.value = false;
    } else {
        visible_extension_dialog.value = false;
        wallet_message.value = true;
    }
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
