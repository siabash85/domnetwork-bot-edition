<template>
    <div>
        <div class="flex justify-between mb-12 items-center">
            <h2 class="text-xl">لیست برنامه ها</h2>
            <v-btn
                :to="{
                    name: 'panel-platforms-clients-create',
                    params: { id: route.params.id },
                }"
                color="blue-accent-2"
            >
                ایجاد برنامه
            </v-btn>
        </div>
        <v-table fixed-header height="700px">
            <thead>
                <tr>
                    <th class="text-right">نام</th>
                    <th class="text-right">ویدیو</th>
                    <th class="text-right">وضعیت</th>
                    <th class="text-right">عملیات</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in clients" :key="item.name">
                    <td>
                        <div class="whitespace-nowrap">
                            {{ item?.name }}
                        </div>
                    </td>

                    <td>
                        <div class="whitespace-nowrap">
                            <a
                                class="text-blue-500"
                                target="_blank"
                                :href="item?.video"
                                >نمایش ویدیو</a
                            >
                        </div>
                    </td>

                    <td>
                        <div class="whitespace-nowrap">
                            <template v-if="item.status == 'active'">
                                <v-chip color="green" text-color="white">
                                    فعال
                                </v-chip>
                            </template>
                            <template v-if="item.status == 'inactive'">
                                <v-chip color="warning" text-color="white">
                                    غیرفعال
                                </v-chip>
                            </template>
                        </div>
                    </td>

                    <td>
                        <div class="flex items-center">
                            <v-btn
                                :to="{
                                    name: 'panel-platforms-clients-edit',
                                    params: {
                                        platform: route.params.id,
                                        id: item.id,
                                    },
                                }"
                                prepend-icon="mdi-pencil-box-outline"
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
            برنامه با موفقیت حذف شد.
        </v-snackbar>
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from "vue";
import ApiService from "@/Core/services/ApiService";
import { useRoute } from "vue-router";
const visible_delete_confirmation = ref(false);
const visible_delete_message = ref(false);
const route = useRoute();
const clients = ref([]);
const selected_item = ref(null);
const fetchData = async () => {
    const { data } = await ApiService.get(
        `/api/panel/guide/platform/${route.params.id}/clients`
    );
    clients.value = data.data;
};
const handleShowDeleteMessage = (item) => {
    visible_delete_confirmation.value = true;
    selected_item.value = item;
};

const handleDelete = async () => {
    const { data } = await ApiService.delete(
        `/api/panel/guide/platform/${route.params.id}/clients/${selected_item.value.id}`
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
