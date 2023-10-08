<template>
    <div class="login-card">
        <v-img
            class="mx-auto my-6"
            max-width="128"
            src="/panel/media/logo.jpg"
        ></v-img>

        <v-card
            class="mx-auto pa-12 pb-8"
            elevation="8"
            max-width="448"
            rounded="lg"
        >
            <div class="text-subtitle-1 text-medium-emphasis">ایمیل</div>

            <v-text-field
                v-model="form.email"
                density="compact"
                placeholder="ایمیل را وارد کنید"
                prepend-inner-icon="mdi-email-outline"
                variant="outlined"
            ></v-text-field>

            <div
                class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between"
            >
                رمز عبور
            </div>

            <v-text-field
                v-model="form.password"
                :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
                :type="visible ? 'text' : 'password'"
                density="compact"
                placeholder="رمز عبور خود را وارد کنید"
                prepend-inner-icon="mdi-lock-outline"
                variant="outlined"
                @click:append-inner="visible = !visible"
            ></v-text-field>

            <v-btn
                @click="submit"
                block
                class="mt-8"
                color="blue"
                size="large"
                variant="tonal"
            >
                ورود
            </v-btn>
        </v-card>
        <v-snackbar :timeout="2000" v-model="snackbar_message">
            ایمیل و یا رمز عبور نادرست است.
        </v-snackbar>
    </div>
</template>
<script setup lang="ts">
import { useAuthStore, type User } from "@/stores/auth";
import { ref } from "vue";
import { useRouter } from "vue-router";
const snackbar_message = ref(false);
const store = useAuthStore();
const router = useRouter();
const form = ref({
    email: "",
    password: "",
});
const visible = ref(false);
const submit = async () => {
    const data = {
        email: form.value.email,
        password: form.value.password,
    };
    try {
        const response = await store.login(data);
        console.log("response.data", response.success);

        if (response.success) {
            console.log("wowww");

            router.push({ name: "panel-dashboard" });
        } else {
            snackbar_message.value = true;
        }
    } catch (error) {
        console.log("error", error);

        snackbar_message.value = true;
    }
};
</script>
