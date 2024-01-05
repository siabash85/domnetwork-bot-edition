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
                <div class="grid grid-cols-12 gap-4 my-6">
                    <div class="col-span-12 lg:col-span-6">
                        <v-card class="">
                            <v-card-item title="گزارش فروش کل">
                                <template v-slot:subtitle>
                                    <v-icon
                                        icon="mdi-alert"
                                        size="18"
                                        color="error"
                                        class="me-1 pb-1"
                                    ></v-icon>
                                    گزارش فروش کل
                                </template>
                            </v-card-item>

                            <v-card-text class="py-0">
                                <v-row align="center" no-gutters>
                                    <v-col cols="6" class="text-right">
                                        <v-icon
                                            color="green"
                                            icon="mdi-cash"
                                            size="88"
                                        ></v-icon>
                                    </v-col>
                                    <v-col class="text-xl text-left" cols="6">
                                        {{
                                            $filters.separate(
                                                statics?.orders_total
                                            )
                                        }}
                                        تومان
                                    </v-col>
                                </v-row>
                            </v-card-text>

                            <div class="d-flex py-3 justify-space-between">
                                <v-list-item density="compact">
                                    <v-list-item-subtitle>
                                        تعداد کل سفارشات :
                                        {{ statics?.orders_count }}
                                    </v-list-item-subtitle>
                                </v-list-item>
                            </div>

                            <v-card-actions>
                                <v-btn :to="{ name: 'panel-orders-index' }"
                                    >مشاهده سفارشات
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </div>
                    <div class="col-span-12 lg:col-span-6">
                        <v-card class="">
                            <v-card-item title="کاربران">
                                <template v-slot:subtitle>
                                    <v-icon
                                        icon="mdi-alert"
                                        size="18"
                                        color="error"
                                        class="me-1 pb-1"
                                    ></v-icon>
                                    تعداد کل کاربران
                                </template>
                            </v-card-item>

                            <v-card-text class="py-0">
                                <v-row align="center" no-gutters>
                                    <v-col cols="6" class="text-right">
                                        <v-icon
                                            color="primary"
                                            icon="mdi-account-group"
                                            size="88"
                                        ></v-icon>
                                    </v-col>
                                    <v-col class="text-xl text-left" cols="6">
                                        {{ statics?.users_count }}
                                    </v-col>
                                </v-row>
                            </v-card-text>

                            <div class="d-flex py-3 justify-space-between">
                                <v-list-item density="compact">
                                    <v-list-item-subtitle>
                                        تعداد کل سرویس های فعال کاربران :
                                        {{ statics?.active_services }}
                                    </v-list-item-subtitle>
                                </v-list-item>
                            </div>

                            <v-card-actions>
                                <v-btn :to="{ name: 'panel-users-index' }"
                                    >مشاهده کاربران
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between mb-12 items-center">
                        <h2 class="text-xl">لیست کاربران</h2>
                    </div>
                    <v-table fixed-header>
                        <thead>
                            <tr>
                                <th class="text-right whitespace-nowrap">
                                    نام کاربری
                                </th>
                                <th class="text-right whitespace-nowrap">
                                    نام
                                </th>
                                <th class="text-right whitespace-nowrap">
                                    ایمیل
                                </th>

                                <th class="text-right whitespace-nowrap">
                                    آیدی تلگرام
                                </th>
                                <th class="text-right whitespace-nowrap">
                                    کیف پول
                                </th>
                                <th class="text-right whitespace-nowrap">
                                    وضعیت
                                </th>
                                <th class="text-right whitespace-nowrap">
                                    زمان ایجاد
                                </th>
                                <th class="text-right whitespace-nowrap">
                                    عملیات
                                </th>
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
                                        <template
                                            v-if="item.status == 'active'"
                                        >
                                            <v-chip
                                                color="green"
                                                text-color="white"
                                            >
                                                فعال
                                            </v-chip>
                                        </template>
                                        <template v-if="item.status == 'ban'">
                                            <v-chip
                                                color="red"
                                                text-color="white"
                                            >
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
                </div>
            </template>
        </base-skeleton>
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from "vue";
import ApiService from "@/Core/services/ApiService";
import { BaseSkeleton, BaseSkeletonItem } from "@/Components/skeleton";
import { useAuthStore, type User } from "@/stores/auth";
import { storeToRefs } from "pinia";
import { useRoute } from "vue-router";
const store = useAuthStore();
const { user } = storeToRefs(store);
const loading = ref(true);

const users = ref([]);
const statics = ref({});
const route = useRoute();
const fetchData = async () => {
    const { data } = await ApiService.get(
        `/api/panel/user/report/${route.params.id}`
    );
    users.value = data.data.users;
    statics.value = data.data.statics;
    loading.value = false;
};

onMounted(() => {
    fetchData();
});
</script>

<style scoped></style>
