<template>
    <div>
        <base-skeleton animated :loading="loading">
            <template #template>
                <div class="grid grid-cols-12 gap-4">
                    <div
                        class="col-span-12 lg:col-span-4"
                        v-for="(item, index) in 3"
                        :key="index"
                    >
                        <base-skeleton-item
                            variant="card"
                            class="h-[270px]"
                        ></base-skeleton-item>
                    </div>
                </div>
            </template>
            <template #default>
                <div class="grid grid-cols-12 gap-4">
                    <div
                        class="col-span-12"
                        :class="[
                            !user?.is_partner
                                ? 'lg:col-span-4'
                                : 'lg:col-span-6',
                        ]"
                    >
                        <template v-if="!user?.is_partner">
                            <v-card class="">
                                <v-card-item title="فروش کل">
                                    <template v-slot:subtitle>
                                        <v-icon
                                            icon="mdi-alert"
                                            size="18"
                                            color="error"
                                            class="me-1 pb-1"
                                        ></v-icon>
                                        میزان فروش کل سفارشات موفق
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
                                        <v-col
                                            class="text-xl text-left"
                                            cols="6"
                                        >
                                            {{
                                                $filters.separate(
                                                    statics?.payments_total
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
                                            {{ statics?.payments_count }}
                                        </v-list-item-subtitle>
                                    </v-list-item>
                                </div>

                                <v-card-actions>
                                    <v-btn
                                        :to="{ name: 'panel-payments-index' }"
                                        >مشاهده تراکنش ها
                                    </v-btn>
                                </v-card-actions>
                            </v-card>
                        </template>
                        <template v-else>
                            <v-card class="">
                                <v-card-item title="گزارش سفارشات">
                                    <template v-slot:subtitle>
                                        <v-icon
                                            icon="mdi-alert"
                                            size="18"
                                            color="error"
                                            class="me-1 pb-1"
                                        ></v-icon>
                                        گزارش سفارشات
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
                                        <v-col
                                            class="text-xl text-left"
                                            cols="6"
                                        >
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
                        </template>
                    </div>
                    <div
                        class="col-span-12"
                        :class="[
                            !user?.is_partner
                                ? 'lg:col-span-4'
                                : 'lg:col-span-6',
                        ]"
                    >
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
                    <div
                        class="col-span-12 lg:col-span-4"
                        v-if="!user?.is_partner"
                    >
                        <v-card class="">
                            <v-card-item title="پیام های پشتیبانی">
                                <template v-slot:subtitle>
                                    <v-icon
                                        icon="mdi-alert"
                                        size="18"
                                        color="error"
                                        class="me-1 pb-1"
                                    ></v-icon>
                                    تعداد کل پیام های پشتیبانی
                                </template>
                            </v-card-item>

                            <v-card-text class="py-0">
                                <v-row align="center" no-gutters>
                                    <v-col cols="6" class="text-right">
                                        <v-icon
                                            color="gray"
                                            icon="mdi-message-reply-text-outline"
                                            size="88"
                                        ></v-icon>
                                    </v-col>
                                    <v-col class="text-xl text-left" cols="6">
                                        {{ statics?.messages_count }}
                                    </v-col>
                                </v-row>
                            </v-card-text>

                            <div class="d-flex py-3 justify-space-between">
                                <v-list-item density="compact">
                                    <v-list-item-subtitle>
                                        پاسخ داده شده :
                                        {{ statics?.answered_messages_count }}
                                    </v-list-item-subtitle>
                                </v-list-item>
                                <v-list-item density="compact">
                                    <v-list-item-subtitle>
                                        پاسخ داده نشده :
                                        {{ statics?.pending_messages_count }}
                                    </v-list-item-subtitle>
                                </v-list-item>
                            </div>

                            <v-card-actions>
                                <v-btn :to="{ name: 'panel-messages-index' }"
                                    >مشاهده پیام ها
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </div>
                </div>
            </template>
        </base-skeleton>
    </div>
</template>
<script setup lang="ts">
import { onMounted, ref } from "vue";
import { BaseSkeleton, BaseSkeletonItem } from "@/Components/skeleton";
import ApiService from "@/Core/services/ApiService";
import { useAuthStore, type User } from "@/stores/auth";
import { storeToRefs } from "pinia";
const store = useAuthStore();
const { user } = storeToRefs(store);
const statics = ref({});
const loading = ref(true);
const fetchData = async () => {
    const { data } = await ApiService.get("api/panel/dashboard");
    statics.value = data.data;
    loading.value = false;
};

onMounted(() => {
    fetchData();
});
</script>
