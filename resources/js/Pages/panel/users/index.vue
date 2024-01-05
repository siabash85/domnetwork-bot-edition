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
                <v-table fixed-header>
                    <thead>
                        <tr>
                            <th class="text-right whitespace-nowrap">
                                نام کاربری
                            </th>
                            <th class="text-right whitespace-nowrap">نام</th>
                            <th class="text-right whitespace-nowrap">ایمیل</th>
                            <th class="text-right whitespace-nowrap">
                                سطح کاربری
                            </th>
                            <th class="text-right whitespace-nowrap">
                                آیدی تلگرام
                            </th>
                            <th class="text-right whitespace-nowrap">
                                کیف پول
                            </th>
                            <th class="text-right whitespace-nowrap">وضعیت</th>
                            <th class="text-right whitespace-nowrap">
                                زمان ایجاد
                            </th>
                            <th class="text-right whitespace-nowrap">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in users" :key="item.id">
                            <td>
                                <div class="whitespace-nowrap">
                                    {{ item?.username }}
                                </div>
                            </td>
                            <td>
                                <div class="whitespace-nowrap">
                                    {{ item?.first_name }}
                                </div>
                            </td>
                            <td>
                                <div class="whitespace-nowrap">
                                    {{ item?.email }}
                                </div>
                            </td>
                            <td>
                                <div class="whitespace-nowrap">
                                    <template v-if="item.is_superuser">
                                        <v-chip
                                            color="primary"
                                            text-color="white"
                                        >
                                            ادمین
                                        </v-chip>
                                    </template>
                                    <template v-else>
                                        <v-chip text-color="white">
                                            کاربر عادی
                                        </v-chip>
                                    </template>
                                </div>
                            </td>
                            <td>
                                <div class="whitespace-nowrap">
                                    {{ item?.uid }}
                                </div>
                            </td>
                            <td>
                                <div class="whitespace-nowrap">
                                    {{ item?.wallet }}
                                </div>
                            </td>

                            <td>
                                <div class="whitespace-nowrap">
                                    <template v-if="item.status == 'active'">
                                        <v-chip
                                            color="green"
                                            text-color="white"
                                        >
                                            فعال
                                        </v-chip>
                                    </template>
                                    <template v-if="item.status == 'ban'">
                                        <v-chip color="red" text-color="white">
                                            مسدود شده
                                        </v-chip>
                                    </template>
                                </div>
                            </td>
                            <td>
                                <div class="whitespace-nowrap">
                                    {{ item?.created_at }}
                                </div>
                            </td>

                            <td>
                                <div class="flex items-center">
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
                            </td>
                        </tr>
                    </tbody>
                </v-table>
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

const loading = ref(true);

const visible_delete_confirmation = ref(false);
const visible_delete_message = ref(false);

const users = ref([]);
const selected_item = ref(null);
const fetchData = async () => {
    const { data } = await ApiService.get("/api/panel/users");
    users.value = data.data;
    loading.value = false;
};
const handleShowDeleteMessage = (item) => {
    visible_delete_confirmation.value = true;
    selected_item.value = item;
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
