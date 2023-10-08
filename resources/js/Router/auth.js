export default {
    path: "/auth",
    meta: {
        auth: true,
    },
    component: () => import("@/Layouts/Auth.vue"),
    children: [
        {
            path: "login",
            name: "auth-login",
            component: () => import("@/Pages/auth/login.vue"),
        },

    ],
};
