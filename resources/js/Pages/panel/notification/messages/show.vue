<template>
    <div>
        <v-sheet>
            <div class="flex justify-between mb-6 items-center">
                <h2 class="text-xl">نمایش اعلان</h2>
                <v-btn :to="{ name: 'panel-notification-messages-index' }">
                    لیست پیام ها
                </v-btn>
            </div>
            <v-form
                ref="formRef"
                validate-on="submit"
                @submit.prevent="handleCreate"
            >
                <div class="grid grid-cols-12 gap-2 space-y-4">
                    <div class="col-span-12">
                        <v-text-field
                            v-model="form.title"
                            :rules="rules"
                            label="عنوان"
                            density="compact"
                            single-line
                            variant="solo"
                            readonly
                        ></v-text-field>
                    </div>

                    <div class="col-span-12">
                        <v-textarea
                            readonly
                            v-model="form.content"
                            label="متن پیام"
                            variant="solo"
                            size="large"
                            single-line
                            hide-details="auto"
                        ></v-textarea>
                    </div>
                </div>
            </v-form>
        </v-sheet>
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watchEffect } from "vue";
import ApiService from "@/Core/services/ApiService";
import { useRoute, useRouter } from "vue-router";

const loading = ref(false);
const formRef = ref(null);
const form = ref({
    title: null,
    content: null,
});
const rules = ref([
    (value) => {
        if (value) return true;
        return "  عنوان  الزامی می باشد";
    },
]);

const selected_users = ref([]);
const users = ref([]);
const router = useRouter();
const route = useRoute();
const handleCreate = async (event) => {
    const { valid } = await formRef.value.validate();
    if (valid) {
        loading.value = true;
        const form_data = new FormData();
        form_data.append("title", form.value.title);
        form_data.append("content", form.value.content);
        form_data.append("users", selected_users.value);

        const { data } = await ApiService.post(
            `/api/panel/notification/user/messages`,
            form_data
        );
        if (data.status == 200) {
            router.push({ name: "panel-notification-messages-index" });
        }
    }
};

const fetchData = async () => {
    let { data: users_res } = await ApiService.get(
        `/api/panel/user/select/search`
    );
    users.value = users_res.data;

    let { data } = await ApiService.get(
        `/api/panel/notification/user/messages/${route.params.id}`
    );
    form.value = data.data;
};

// watchEffect(() => {
//     if (formRef.value) {
//         formRef.value.setValues({
//             ...form.value,
//         });
//     }
// });

onMounted(() => {
    fetchData();
});
</script>

<style scoped></style>
