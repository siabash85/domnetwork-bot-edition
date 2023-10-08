<template>
    <div>
        <div class="flex justify-between mb-12 items-center">
            <h2 class="text-xl">لیست گروه تمرینات</h2>
            <v-btn
                :to="{
                    name: 'panel-forms-exercises-create',
                    params: { id: route.params.id },
                }"
                color="blue-accent-2"
            >
                ایجاد گروه تمرینی
            </v-btn>
        </div>
        <div class="overflow-x-auto w-full">
            <v-table>
                <thead>
                    <tr>
                        <th class="text-right whitespace-nowrap">شماره</th>
                        <th class="text-right whitespace-nowrap">کلاس</th>
                        <th class="text-right whitespace-nowrap">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in programs" :key="item.id">
                        <td>{{ item.id }}</td>
                        <td>
                            <template v-if="item.class == 1">A</template>
                            <template v-if="item.class == 2">B</template>
                            <template v-if="item.class == 3">C</template>
                            <template v-if="item.class == 4">D</template>
                        </td>
                        <td>
                            <div class="flex items-center">
                                <v-btn
                                    :to="{
                                        name: 'panel-forms-exercises-edit',
                                        params: {
                                            id: route.params.id,
                                            exercise: item.id,
                                        },
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
import { useRoute, useRouter } from "vue-router";
const visible_delete_confirmation = ref(false);
const visible_delete_message = ref(false);
const route = useRoute();
const router = useRouter();
const programs = ref([]);
const selected_item = ref(null);
const fetchData = async () => {
    const { data } = await ApiService.get(
        `/api/panel/form/${route.params.id}/programs`
    );
    programs.value = data.data;
};
const handleShowDeleteMessage = (item) => {
    visible_delete_confirmation.value = true;
    selected_item.value = item;
};

const handleDelete = async () => {
    const { data } = await ApiService.delete(
        `/api/panel/form/${route.params.id}/programs/${selected_item.value.id}`
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
