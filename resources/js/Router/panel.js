export default {
    path: "/panel",
    meta: {
        middleware: "auth",
    },
    component: () => import("@/Layouts/Panel.vue"),
    children: [
        {
            path: "dashboard",
            name: "panel-dashboard",
            component: () => import("@/Pages/panel/dashboard.vue"),
        },

        // movements
        {
            path: "movements",
            name: "panel-movements-index",
            component: () => import("@/Pages/panel/movements/index.vue"),
        },
        {
            path: "movements/create",
            name: "panel-movements-create",
            component: () => import("@/Pages/panel/movements/create.vue"),
        },
        {
            path: "movements/edit/:id",
            name: "panel-movements-edit",
            component: () => import("@/Pages/panel/movements/edit.vue"),
        },

    ],
};
