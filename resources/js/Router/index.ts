import { createRouter, createWebHistory, createWebHashHistory, RouteRecordRaw } from "vue-router";
import panel from "@/Router/panel";
import auth from "@/Router/auth";
import { useAuthStore } from "@/stores/auth";

const routes: Array<RouteRecordRaw> = [panel, auth];


const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();

    if (to.name !== 'auth-login') {
        authStore.verifyAuth();
    }

    if (to.meta.middleware == "auth" && !authStore.isAuthenticated) {
        next({ name: "auth-login" });
    }
    if (to.meta.middleware == "guest" && authStore.isAuthenticated) {
        next({ name: "panel-dashboard" });
    }
    next();

    // Scroll page to top on every route change
    window.scrollTo({
        top: 0,
        left: 0,
        behavior: "smooth",
    });
});


export default router;

