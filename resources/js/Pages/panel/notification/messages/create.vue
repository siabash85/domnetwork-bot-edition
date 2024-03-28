<template>
    <div>
        <v-sheet>
            <div class="mb-6">
                <h2 class="text-xl">ارسال اعلان</h2>
            </div>
            <v-form
                ref="formRef"
                validate-on="submit"
                @submit.prevent="handleCreate"
            >
                <div class="grid grid-cols-12 gap-2 space-y-4">
                    <div class="col-span-12">
                        <v-autocomplete
                            no-data-text="نتیجه ای یافت نشد"
                            v-model="selected_users"
                            :items="users"
                            chips
                            closable-chips
                            color="blue-grey-lighten-2"
                            item-title="uid"
                            item-value="id"
                            label="انتخاب کاربر"
                            multiple
                            hint="در صورت عدم انتخاب کاربری اعلان به کل کاربران ارسال خواهد شد"
                            persistent-hint
                        >
                            <template v-slot:item="{ props, item }">
                                <v-list-item
                                    v-bind="props"
                                    :title="`${item.raw.first_name} ${item.raw.username}`"
                                    :subtitle="item.raw.uid"
                                ></v-list-item>
                            </template>
                        </v-autocomplete>
                    </div>
                    <div class="col-span-12">
                        <v-text-field
                            v-model="form.title"
                            :rules="rules"
                            label="عنوان"
                            density="compact"
                            single-line
                        ></v-text-field>
                    </div>

                    <div class="col-span-12">
                        <v-textarea
                            v-model="form.content"
                            label="متن پیام"
                            size="large"
                            single-line
                            hide-details="auto"
                        ></v-textarea>
                    </div>
                </div>

                <v-btn
                    :loading="loading"
                    color="light-blue-accent-4"
                    type="submit"
                    block
                    class="mt-2"
                    >ارسال
                </v-btn>
            </v-form>
        </v-sheet>
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from "vue";
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
};

onMounted(() => {
    fetchData();
});
</script>

<style scoped></style>
