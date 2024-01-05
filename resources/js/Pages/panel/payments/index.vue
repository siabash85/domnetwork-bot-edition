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
                    <h2 class="text-xl">لیست تراکنش ها</h2>
                </div>
                <!-- <v-table fixed-header>
                    <thead>
                        <tr>
                            <th class="text-right whitespace-nowrap">کاربر</th>
                            <th class="text-right whitespace-nowrap">مبلغ</th>
                            <th class="text-right whitespace-nowrap">
                                روش پرداخت
                            </th>
                            <th class="text-right whitespace-nowrap">
                                کد پیگیری
                            </th>
                            <th class="text-right whitespace-nowrap">وضعیت</th>
                            <th class="text-right whitespace-nowrap">تاریخ</th>
                            <th class="text-right whitespace-nowrap">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in payments" :key="item.name">
                            <td>
                                <div class="whitespace-nowrap">
                                    {{ item.user?.username }}
                                </div>
                            </td>
                            <td>
                                <div class="whitespace-nowrap">
                                    {{ item.amount }} تومان
                                </div>
                            </td>
                            <td>
                                <div class="whitespace-nowrap">
                                    {{ item.payment_method?.title }}
                                </div>
                            </td>
                            <td>
                                <div class="whitespace-nowrap">
                                    {{ item.reference_code }}
                                </div>
                            </td>

                            <td>
                                <div class="whitespace-nowrap">
                                    <template v-if="item.status == 'pending'">
                                        <v-chip
                                            color="warning"
                                            text-color="white"
                                        >
                                            در انتظار پرداخت
                                        </v-chip>
                                    </template>
                                    <template v-if="item.status == 'success'">
                                        <v-chip
                                            color="green"
                                            text-color="white"
                                        >
                                            پرداخت شده
                                        </v-chip>
                                    </template>
                                    <template v-if="item.status == 'rejected'">
                                        <v-chip color="red" text-color="white">
                                            لغو شده
                                        </v-chip>
                                    </template>
                                    <template
                                        v-if="
                                            item.status ==
                                            'pending_confirmation'
                                        "
                                    >
                                        <v-chip
                                            color="warning"
                                            text-color="white"
                                        >
                                            در انتظار تایید رسید پرداخت
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
                                <div class="flex items-center">
                                    <v-btn
                                        :to="{
                                            name: 'panel-payments-edit',
                                            params: { id: item.id },
                                        }"
                                        prepend-icon="mdi-pencil-box-outline"
                                    >
                                        ویرایش
                                    </v-btn>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </v-table> -->
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
                        :items="payments"
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
                            <div class="whitespace-nowrap">کاربر</div>
                        </template>
                        <template v-slot:header.amount>
                            <div class="whitespace-nowrap">مبلغ</div>
                        </template>
                        <template v-slot:header.payment_method>
                            <div class="whitespace-nowrap">روش پرداخت</div>
                        </template>

                        <template v-slot:header.reference_code>
                            <div class="whitespace-nowrap">کد پیگیری</div>
                        </template>
                        <template v-slot:header.status>
                            <div class="whitespace-nowrap">وضعیت</div>
                        </template>
                        <template v-slot:header.created_at>
                            <div class="whitespace-nowrap">تاریخ</div>
                        </template>
                        <template v-slot:header.actions>
                            <div class="whitespace-nowrap">عملیات</div>
                        </template>

                        <template v-slot:item.username="{ item }">
                            <div class="whitespace-nowrap">
                                {{ item.user?.username }}
                            </div>
                        </template>
                        <template v-slot:item.amount="{ item }">
                            <div class="whitespace-nowrap">
                                <v-chip color="green" text-color="white">
                                    {{ $filters.separate(item?.amount) }} تومان
                                </v-chip>
                            </div>
                        </template>
                        <template v-slot:item.payment_method="{ item }">
                            <div class="whitespace-nowrap">
                                {{ item.payment_method?.title }}
                            </div>
                        </template>
                        <template v-slot:item.reference_code="{ item }">
                            <div class="whitespace-nowrap">
                                {{ item.reference_code }}
                            </div>
                        </template>
                        <template v-slot:item.status="{ item }">
                            <div class="whitespace-nowrap">
                                <template v-if="item.status == 'pending'">
                                    <v-chip color="warning" text-color="white">
                                        در انتظار پرداخت
                                    </v-chip>
                                </template>
                                <template v-if="item.status == 'success'">
                                    <v-chip color="green" text-color="white">
                                        پرداخت شده
                                    </v-chip>
                                </template>
                                <template v-if="item.status == 'rejected'">
                                    <v-chip color="red" text-color="white">
                                        لغو شده
                                    </v-chip>
                                </template>
                                <template
                                    v-if="item.status == 'pending_confirmation'"
                                >
                                    <v-chip color="warning" text-color="white">
                                        در انتظار تایید رسید پرداخت
                                    </v-chip>
                                </template>
                            </div>
                        </template>
                        <template v-slot:item.created_at="{ item }">
                            <div class="whitespace-nowrap">
                                {{ item.created_at }}
                            </div>
                        </template>

                        <template v-slot:item.actions="{ item }">
                            <div class="flex items-center">
                                <v-btn
                                    :to="{
                                        name: 'panel-payments-edit',
                                        params: { id: item.id },
                                    }"
                                    prepend-icon="mdi-pencil-box-outline"
                                >
                                    ویرایش
                                </v-btn>
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
const search = ref("");
const headers = ref([
    { key: "username", title: "نام ", sortable: true },
    { key: "amount", title: "مبلغ", sortable: true },
    { key: "payment_method", title: "روش پرداخت", sortable: true },
    { key: "reference_code", title: "کد پیگیری", sortable: true },
    { key: "status", title: "وضعیت", sortable: true },
    { key: "created_at", title: "تاریخ", sortable: true },
    { key: "actions", title: "عملیات", sortable: true },
]);
const payments = ref([]);
const selected_item = ref(null);
const fetchData = async () => {
    const { data } = await ApiService.get("/api/panel/payments");
    payments.value = data.data;
    loading.value = false;
};
const handleShowDeleteMessage = (item) => {
    visible_delete_confirmation.value = true;
    selected_item.value = item;
};

const handleDelete = async () => {
    const { data } = await ApiService.delete(
        `/api/panel/payments/${selected_item.value.id}`
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
