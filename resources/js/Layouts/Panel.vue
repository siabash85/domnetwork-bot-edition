<template>
    <v-layout>
        <v-navigation-drawer
            :temporary="mobile"
            :permanent="!mobile"
            v-model="drawer"
            location="right"
        >
            <template v-slot:prepend>
                <template v-if="!user?.is_partner">
                    <v-list-item
                        lines="two"
                        prepend-avatar="/panel/media/avatars/blank.png"
                        :title="user?.username"
                        subtitle="کاربر ادمین"
                    ></v-list-item>
                </template>
                <templatev-else>
                    <v-list-item
                        lines="two"
                        prepend-avatar="/panel/media/avatars/blank.png"
                        :title="user?.username"
                        subtitle="کاربر همکار"
                    ></v-list-item>
                </templatev-else>
            </template>
            <template v-slot:append>
                <div class="pa-2">
                    <v-btn @click="signOut" block> خروج از حساب کاربری </v-btn>
                </div>
            </template>

            <v-divider></v-divider>

            <v-list density="compact" nav>
                <v-list-item
                    link
                    prepend-icon="mdi-home-city"
                    title="داشبورد"
                    :to="{ name: 'panel-dashboard' }"
                >
                </v-list-item>

                <v-list-item
                    v-if="!user?.is_partner"
                    prepend-icon="mdi-server-outline"
                    title="سرورها"
                    value="servers"
                    :to="{ name: 'panel-servers-index' }"
                ></v-list-item>

                <v-list-item
                    v-if="!user?.is_partner"
                    prepend-icon="mdi-calendar-clock"
                    title="بازه های زمانی"
                    value="durations"
                    :to="{ name: 'panel-durations-index' }"
                ></v-list-item>
                <v-list-item
                    v-if="!user?.is_partner"
                    prepend-icon="mdi-package-variant-closed"
                    title="پکیج ها"
                    value="packages"
                    :to="{ name: 'panel-packages-index' }"
                ></v-list-item>
                <v-list-item
                    v-if="!user?.is_partner"
                    prepend-icon="mdi-access-point"
                    title=" مدیریت سرویس ها"
                    value="services"
                    :to="{ name: 'panel-services-index' }"
                ></v-list-item>
                <v-list-item
                    prepend-icon="mdi-basket-outline"
                    title=" مدیریت سفارشات "
                    value="orders"
                    :to="{ name: 'panel-orders-index' }"
                ></v-list-item>
                <v-list-item
                    v-if="!user?.is_partner"
                    prepend-icon="mdi-credit-card-outline"
                    title=" مدیریت تراکنش ها"
                    value="payments"
                    :to="{ name: 'panel-payments-index' }"
                ></v-list-item>
                <v-list-item
                    prepend-icon="mdi-account-star"
                    title=" مدیریت اشتراک ها "
                    value="subscriptions"
                    :to="{ name: 'panel-subscriptions-index' }"
                ></v-list-item>
                <v-list-item
                    v-if="!user?.is_partner"
                    prepend-icon="mdi-connection"
                    title="راهنمای اتصال"
                    value="platforms"
                    :to="{ name: 'panel-platforms-index' }"
                ></v-list-item>
                <v-list-item
                    v-if="!user?.is_partner"
                    prepend-icon="mdi-lifebuoy"
                    title="پیام های پشتیبانی"
                    value="messages"
                    :to="{ name: 'panel-messages-index' }"
                ></v-list-item>
                <v-list-item
                    v-if="!user?.is_partner"
                    prepend-icon="mdi-cash-multiple"
                    title="تعرفه ها"
                    value="pricing"
                    :to="{ name: 'panel-pricing-index' }"
                ></v-list-item>
                <v-list-item
                    prepend-icon="mdi-account-group"
                    title="کاربران"
                    value="users"
                    :to="{ name: 'panel-users-index' }"
                ></v-list-item>
                <v-list-item
                    v-if="!user?.is_partner"
                    prepend-icon="mdi-cog-outline"
                    title="تنظیمات"
                    value="settings"
                    :to="{ name: 'panel-settings-index' }"
                ></v-list-item>
            </v-list>
        </v-navigation-drawer>
        <v-app-bar title="">
            <v-app-bar-nav-icon
                variant="text"
                @click.stop="drawer = !drawer"
            ></v-app-bar-nav-icon>
        </v-app-bar>
        <v-main>
            <div class="p-5">
                <router-view></router-view>
            </div>
        </v-main>
    </v-layout>
</template>
<script setup lang="ts">
import { onMounted, ref } from "vue";
import { useDisplay } from "vuetify";
import { useAuthStore, type User } from "@/stores/auth";
import { useRouter } from "vue-router";
import { storeToRefs } from "pinia";
const router = useRouter();
const store = useAuthStore();
const { user } = storeToRefs(store);

const { mobile, smAndDown, mdAndDown } = useDisplay();

const drawer = ref(true);
const signOut = () => {
    store.logout().then(() => {
        router.push({ name: "auth-login" });
    });
};
onMounted(() => {
    if (mobile.value) {
        drawer.value = false;
    }
});
</script>

<style></style>
