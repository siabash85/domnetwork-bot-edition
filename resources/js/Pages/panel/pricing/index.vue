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
                    <h2 class="text-xl">لیست تعرفه ها</h2>
                    <v-btn
                        :to="{ name: 'panel-pricing-create' }"
                        color="blue-accent-2"
                    >
                        ایجاد تعرفه
                    </v-btn>
                </div>
                <v-table fixed-header height="700px">
                    <thead>
                        <tr>
                            <th class="text-right">نام</th>
                            <th class="text-right">مقدار پیش فرض</th>
                            <th class="text-right">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in pricing" :key="item.name">
                            <td>
                                <div class="whitespace-nowrap">
                                    {{ item?.name }}
                                </div>
                            </td>

                            <td>
                                <div class="whitespace-nowrap">
                                    <template v-if="item.is_default">
                                        <v-chip
                                            color="green"
                                            text-color="white"
                                        >
                                            می باشد
                                        </v-chip>
                                    </template>
                                    <template v-else>
                                        <v-chip text-color="white">
                                            نمی باشد
                                        </v-chip>
                                    </template>
                                </div>
                            </td>

                            <td>
                                <div class="flex items-center">
                                    <v-btn
                                        :to="{
                                            name: 'panel-pricing-edit',
                                            params: { id: item.id },
                                        }"
                                        prepend-icon="mdi-pencil-box-outline"
                                        class="mr-4"
                                    >
                                        ویرایش
                                    </v-btn>
                                    <v-btn
                                        @click="handleShowDeleteMessage(item)"
                                        prepend-icon="mdi-trash-can-outline"
                                        class="mr-4"
                                    >
                                        حذف
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
            تعرفه با موفقیت حذف شد.
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

const pricing = ref([]);
const selected_item = ref(null);
const fetchData = async () => {
    const { data } = await ApiService.get("api/panel/pricing");
    pricing.value = data.data;
    loading.value = false;
};
const handleShowDeleteMessage = (item) => {
    visible_delete_confirmation.value = true;
    selected_item.value = item;
};

const handleDelete = async () => {
    const { data } = await ApiService.delete(
        `/api/panel/pricing/${selected_item.value.id}`
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
