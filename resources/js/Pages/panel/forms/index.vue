<template>
    <div>
        <div class="flex justify-between mb-12 items-center">
            <h2 class="text-xl">لیست برنامه ها</h2>
            <v-btn :to="{ name: 'panel-forms-create' }" color="blue-accent-2">
                ایجاد برنامه تمرینی
            </v-btn>
        </div>
        <div class="overflow-x-auto w-full">
            <v-table>
                <thead>
                    <tr>
                        <th class="text-right whitespace-nowrap">شماره</th>
                        <th class="text-right whitespace-nowrap">نام</th>
                        <th class="text-right whitespace-nowrap">
                            نام خانوادگی
                        </th>
                        <th class="text-right whitespace-nowrap">
                            شماره همراه
                        </th>
                        <th class="text-right whitespace-nowrap">قد</th>
                        <th class="text-right whitespace-nowrap">وزن</th>
                        <th class="text-right whitespace-nowrap">سن</th>
                        <th class="text-right whitespace-nowrap">تاریخ صدور</th>
                        <th class="text-right whitespace-nowrap">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in forms" :key="item.id">
                        <td>{{ item.id }}</td>
                        <td>
                            <div class="whitespace-nowrap">
                                {{ item.first_name }}
                            </div>
                        </td>
                        <td>
                            <div class="whitespace-nowrap">
                                {{ item.last_name }}
                            </div>
                        </td>
                        <td>
                            <div class="whitespace-nowrap">
                                {{ item.mobile }}
                            </div>
                        </td>
                        <td>{{ item.height }}</td>
                        <td>{{ item.weight }}</td>
                        <td>{{ item.age }}</td>
                        <td>{{ item.modified_date }}</td>
                        <td>
                            <div class="flex items-center">
                                <v-btn
                                    :to="{
                                        name: 'panel-forms-exercises-index',
                                        params: { id: item.id },
                                    }"
                                    prepend-icon="mdi-list-box-outline"
                                >
                                    مدیریت
                                </v-btn>
                                <v-btn
                                    :to="{
                                        name: 'panel-forms-print',
                                        params: { id: item.id },
                                    }"
                                    class="mr-4"
                                    prepend-icon="mdi-printer-outline"
                                >
                                    چاپ
                                </v-btn>
                                <v-btn
                                    :to="{
                                        name: 'panel-forms-print',
                                        params: { id: item.id },
                                        query: { download: true },
                                    }"
                                    class="mr-4"
                                    prepend-icon="mdi-printer-outline"
                                >
                                    دانلود
                                </v-btn>
                                <v-btn
                                    :to="{
                                        name: 'panel-forms-edit',
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
        </div>

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
            نوع تمرین با موفقیت حذف شد.
        </v-snackbar>
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from "vue";
import ApiService from "@/Core/services/ApiService";
const visible_delete_confirmation = ref(false);
const visible_delete_message = ref(false);

const forms = ref([]);
const selected_item = ref(null);
const fetchData = async () => {
    const { data } = await ApiService.get("/api/panel/forms");
    forms.value = data.data;
};
const handleShowDeleteMessage = (item) => {
    visible_delete_confirmation.value = true;
    selected_item.value = item;
};

const handleDelete = async () => {
    const { data } = await ApiService.delete(
        `/api/panel/forms/${selected_item.value.id}`
    );
    if (data.success) {
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
