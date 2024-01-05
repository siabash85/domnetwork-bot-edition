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
                <v-table fixed-header>
                    <thead>
                        <tr>
                            <th class="text-right whitespace-nowrap">کاربر</th>
                            <th class="text-right whitespace-nowrap">
                                نام سرویس
                            </th>
                            <th class="text-right whitespace-nowrap">
                                کد سرویس
                            </th>
                            <th class="text-right whitespace-nowrap">
                                شناسه سرویس
                            </th>
                            <th class="text-right whitespace-nowrap">uuid</th>
                            <th class="text-right whitespace-nowrap">subId</th>

                            <th class="text-right whitespace-nowrap">سرور</th>
                            <th class="text-right whitespace-nowrap">
                                زمان سرویس
                            </th>
                            <th class="text-right whitespace-nowrap">پکیج</th>

                            <th class="text-right whitespace-nowrap">وضعیت</th>
                            <th class="text-right whitespace-nowrap">
                                تاریخ ایجاد
                            </th>
                            <th class="text-right whitespace-nowrap">
                                تاریخ پایان
                            </th>
                            <th class="text-right whitespace-nowrap">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in subscriptions" :key="item.name">
                            <td>
                                <div class="whitespace-nowrap">
                                    {{ item.user?.username }} -
                                    {{ item.user?.uid }}
                                </div>
                            </td>
                            <td>
                                <div class="whitespace-nowrap">
                                    {{ item.name }}
                                </div>
                            </td>
                            <td>
                                <div class="whitespace-nowrap">
                                    {{ item.code }}
                                </div>
                            </td>
                            <td>
                                <div class="whitespace-nowrap">
                                    {{ item.slug }}
                                </div>
                            </td>
                            <td>
                                <div class="whitespace-nowrap">
                                    {{ item.uuid }}
                                </div>
                            </td>
                            <td>
                                <div class="whitespace-nowrap">
                                    {{ item.subId }}
                                </div>
                            </td>

                            <!-- <td>
                                <div class="whitespace-nowrap">
                                    {{ $filters.separate(item?.price) }}
                                    تومان
                                </div>
                            </td> -->
                            <td>
                                <div class="whitespace-nowrap">
                                    {{ item.service?.server?.name }}
                                </div>
                            </td>
                            <td>
                                <div class="whitespace-nowrap">
                                    {{ item.service?.package_duration?.value }}
                                    روز
                                </div>
                            </td>
                            <td>
                                <div class="whitespace-nowrap">
                                    {{ item.service?.package?.name }}
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
                                    <template v-if="item.status == 'inactive'">
                                        <v-chip color="red" text-color="white">
                                            غیرفعال
                                        </v-chip>
                                    </template>
                                </div>
                            </td>
                            <td>
                                <div class="whitespace-nowrap">
                                    {{ item.created_at }}
                                </div>
                            </td>
                            <td>
                                <div class="whitespace-nowrap">
                                    {{ item.expire_at }}
                                </div>
                            </td>

                            <td>
                                <div class="flex items-center">
                                    <v-btn
                                        :to="{
                                            name: 'panel-subscriptions-edit',
                                            params: { id: item.id },
                                        }"
                                        prepend-icon="mdi-pencil-box-outline"
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
            تراکنش با موفقیت حذف شد.
        </v-snackbar>
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from "vue";
import ApiService from "@/Core/services/ApiService";
const visible_delete_confirmation = ref(false);
const visible_delete_message = ref(false);
import { BaseSkeleton, BaseSkeletonItem } from "@/Components/skeleton";
const loading = ref(true);

const subscriptions = ref([]);
const selected_item = ref(null);
const fetchData = async () => {
    const { data } = await ApiService.get("/api/panel/subscriptions");
    subscriptions.value = data.data;
    loading.value = false;
};
const handleShowDeleteMessage = (item) => {
    visible_delete_confirmation.value = true;
    selected_item.value = item;
};

const handleDelete = async () => {
    const { data } = await ApiService.delete(
        `/api/panel/subscriptions/${selected_item.value.id}`
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
