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
                    <h2 class="text-xl">لیست پیام ها</h2>
                    <v-btn
                        :to="{ name: 'panel-notification-messages-create' }"
                        color="blue-accent-2"
                    >
                        ارسال اعلان
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
                        :items="messages"
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
                        <template v-slot:header.title>
                            <div class="whitespace-nowrap">عنوان</div>
                        </template>

                        <template v-slot:item.actions="{ item }">
                            <div class="whitespace-nowrap">
                                <div class="flex items-center">
                                    <v-btn
                                        :to="{
                                            name: 'panel-notification-messages-show',
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
    </div>
</template>

<script setup lang="ts">
import { BaseSkeleton, BaseSkeletonItem } from "@/Components/skeleton";

import { onMounted, ref } from "vue";
import ApiService from "@/Core/services/ApiService";
const search = ref("");
const headers = ref([
    { key: "title", title: "عنوان", sortable: true },
    { key: "actions", title: "عملیات", sortable: true },
]);
const messages = ref([]);
const selected_item = ref(null);
const fetchData = async () => {
    const { data } = await ApiService.get(
        "/api/panel/notification/user/messages"
    );
    messages.value = data.data;
    loading.value = false;
};

const loading = ref(true);

onMounted(() => {
    fetchData();
});
</script>

<style scoped></style>
