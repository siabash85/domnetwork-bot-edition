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
                    <h2 class="text-xl">لیست کاربران</h2>
                    <v-btn
                        :to="{ name: 'panel-users-create' }"
                        color="blue-accent-2"
                    >
                        ایجاد کاربر
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
                        :items="users"
                        :headers="headers"
                        :select-all="false"
                        :all-selected="false"
                        items-per-page-all-text=""
                        :items-per-page-options="[
                            { value: 5, title: '5' },
                            { value: 10, title: '10' },
                            { value: 25, title: '25' },
                            { value: 50, title: '50' },
                            { value: 100, title: '100' },
                        ]"
                    >
                        <template v-slot:header.username>
                            <div class="whitespace-nowrap">نام کاربری</div>
                        </template>
                        <template v-slot:header.first_name>
                            <div class="whitespace-nowrap">نام</div>
                        </template>

                        <template v-slot:header.email>
                            <div class="whitespace-nowrap">ایمیل</div>
                        </template>
                        <template v-slot:header.is_superuser>
                            <div class="whitespace-nowrap">سطح کاربری</div>
                        </template>
                        <template v-slot:header.uid>
                            <div class="whitespace-nowrap">آیدی تلگرام</div>
                        </template>
                        <template v-slot:header.wallet>
                            <div class="whitespace-nowrap">کیف پول</div>
                        </template>
                        <template v-slot:header.status>
                            <div class="whitespace-nowrap">وضعیت</div>
                        </template>
                        <template v-slot:header.created_at>
                            <div class="whitespace-nowrap">زمان ایجاد</div>
                        </template>
                        <template v-slot:header.actions>
                            <div class="whitespace-nowrap">عملیات</div>
                        </template>

                        <template v-slot:item.username="{ item }">
                            <div class="whitespace-nowrap">
                                {{ item?.username }}
                            </div>
                        </template>
                        <template v-slot:item.first_name="{ item }">
                            <div class="whitespace-nowrap">
                                {{ item?.first_name }}
                            </div>
                        </template>
                        <template v-slot:item.is_superuser="{ item }">
                            <div class="whitespace-nowrap">
                                <template v-if="item.is_superuser">
                                    <v-chip color="primary" text-color="white">
                                        ادمین
                                    </v-chip>
                                </template>
                                <template v-else>
                                    <v-chip text-color="white">
                                        کاربر عادی
                                    </v-chip>
                                </template>
                            </div>
                        </template>
                        <template v-slot:item.uid="{ item }">
                            <div class="whitespace-nowrap">
                                {{ item?.uid }}
                            </div>
                        </template>
                        <template v-slot:item.wallet="{ item }">
                            <div class="whitespace-nowrap">
                                <v-chip color="green" text-color="white">
                                    {{ $filters.separate(item?.wallet) }} تومان
                                </v-chip>
                            </div>
                        </template>
                        <template v-slot:item.status="{ item }">
                            <div class="whitespace-nowrap">
                                <template v-if="item.status == 'active'">
                                    <v-chip color="green" text-color="white">
                                        فعال
                                    </v-chip>
                                </template>
                                <template v-if="item.status == 'ban'">
                                    <v-chip color="red" text-color="white">
                                        مسدود شده
                                    </v-chip>
                                </template>
                            </div>
                        </template>
                        <template v-slot:item.created_at="{ item }">
                            <div class="whitespace-nowrap">
                                {{ item?.created_at }}
                            </div>
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <div class="whitespace-nowrap">
                                <div class="flex items-center">
                                    <div v-if="!user?.is_partner">
                                        <template v-if="item?.is_partner">
                                            <v-btn
                                                :to="{
                                                    name: 'panel-users-report',
                                                    params: { id: item.id },
                                                }"
                                                prepend-icon="mdi-chart-box-outline"
                                                class="mr-4"
                                            >
                                                گزارش
                                            </v-btn>
                                        </template>
                                        <template v-else>
                                            <v-btn
                                                prepend-icon="mdi-chart-box-outline"
                                                class="mr-4"
                                            >
                                                گزارش
                                            </v-btn>
                                        </template>
                                    </div>

                                    <v-btn
                                        :to="{
                                            name: 'panel-users-edit',
                                            params: { id: item.id },
                                        }"
                                        prepend-icon="mdi-pencil-box-outline"
                                        class="mr-4"
                                    >
                                        مشاهده
                                    </v-btn>
                                </div>
                            </div>
                        </template>
                    </v-data-table>
                </v-card>
            </template>
        </base-skeleton>

        <v-dialog
            v-model="visible_delete_confirmation"
            persistent
            width="350px"
        >
            <v-card>
                <v-card-title class="">
                    آیا از حذف این آیتم اطمینان دارید؟
                </v-card-title>
                <v-card-text>آیا از حذف این آیتم اطمینان دارید؟</v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="green-darken-1"
                        variant="text"
                        @click="visible_delete_confirmation = false"
                    >
                        نه
                    </v-btn>
                    <v-btn color="green-darken-1" @click="handleDelete">
                        بله
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-snackbar v-model="visible_delete_message" :timeout="2000">
            کاربر با موفقیت حذف شد.
        </v-snackbar>
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from "vue";
import ApiService from "@/Core/services/ApiService";
import { BaseSkeleton, BaseSkeletonItem } from "@/Components/skeleton";
import { useAuthStore, type User } from "@/stores/auth";
import { storeToRefs } from "pinia";
const store = useAuthStore();
const { user } = storeToRefs(store);
const loading = ref(true);
const search = ref("");
const visible_delete_confirmation = ref(false);
const visible_delete_message = ref(false);
const users = ref([]);
const selected_item = ref(null);
const headers = ref([
    { key: "username", title: "نام کاربری", sortable: true },
    { key: "first_name", title: "نام", sortable: true },
    { key: "email", title: "ایمیل", sortable: true },
    { key: "is_superuser", title: "سطح کاربری", sortable: true },
    { key: "uid", title: "آیدی تلگرام", sortable: true },
    { key: "wallet", title: "کیف پول", sortable: true },
    { key: "status", title: "وضعیت", sortable: true },
    { key: "created_at", title: "زمان ایجاد", sortable: true },
    { key: "actions", title: "عملیات", sortable: true },
]);
const fetchData = async () => {
    const { data } = await ApiService.get("/api/panel/users");
    users.value = data.data;
    loading.value = false;
};
const handleDelete = async () => {
    const { data } = await ApiService.delete(
        `/api/panel/users/${selected_item.value.id}`
    );
    if (data.status == 200) {
        visible_delete_confirmation.value = false;
        fetchData();
        visible_delete_message.value = true;
    }
};
onMounted(() => {
    fetchData();
});
</script>

<style scoped></style>
