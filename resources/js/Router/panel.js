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

        // servers
        {
            path: "servers",
            name: "panel-servers-index",
            component: () => import("@/Pages/panel/servers/index.vue"),
        },
        {
            path: "servers/create",
            name: "panel-servers-create",
            component: () => import("@/Pages/panel/servers/create.vue"),
        },
        {
            path: "servers/edit/:id",
            name: "panel-servers-edit",
            component: () => import("@/Pages/panel/servers/edit.vue"),
        },

        // durations
        {
            path: "durations",
            name: "panel-durations-index",
            component: () => import("@/Pages/panel/durations/index.vue"),
        },
        {
            path: "durations/create",
            name: "panel-durations-create",
            component: () => import("@/Pages/panel/durations/create.vue"),
        },
        {
            path: "durations/edit/:id",
            name: "panel-durations-edit",
            component: () => import("@/Pages/panel/durations/edit.vue"),
        },

        // packages
        {
            path: "packages",
            name: "panel-packages-index",
            component: () => import("@/Pages/panel/packages/index.vue"),
        },
        {
            path: "packages/create",
            name: "panel-packages-create",
            component: () => import("@/Pages/panel/packages/create.vue"),
        },
        {
            path: "packages/edit/:id",
            name: "panel-packages-edit",
            component: () => import("@/Pages/panel/packages/edit.vue"),
        },

        // packages
        {
            path: "services",
            name: "panel-services-index",
            component: () => import("@/Pages/panel/services/index.vue"),
        },
        {
            path: "services/create",
            name: "panel-services-create",
            component: () => import("@/Pages/panel/services/create.vue"),
        },
        {
            path: "services/edit/:id",
            name: "panel-services-edit",
            component: () => import("@/Pages/panel/services/edit.vue"),
        },

        // payments
        {
            path: "payments",
            name: "panel-payments-index",
            component: () => import("@/Pages/panel/payments/index.vue"),
        },

        {
            path: "payments/edit/:id",
            name: "panel-payments-edit",
            component: () => import("@/Pages/panel/payments/edit.vue"),
        },


        // guide platforms

        {
            path: "guide/platforms",
            name: "panel-platforms-index",
            component: () => import("@/Pages/panel/platforms/index.vue"),
        },
        {
            path: "guide/platforms/create",
            name: "panel-platforms-create",
            component: () => import("@/Pages/panel/platforms/create.vue"),
        },
        {
            path: "guide/platforms/edit/:id",
            name: "panel-platforms-edit",
            component: () => import("@/Pages/panel/platforms/edit.vue"),
        },

        // guide platform cients

        {
            path: "guide/platform/:id/clients",
            name: "panel-platforms-clients-index",
            component: () => import("@/Pages/panel/platforms/clients/index.vue"),
        },
        {
            path: "guide/platform/:id/clients/create",
            name: "panel-platforms-clients-create",
            component: () => import("@/Pages/panel/platforms/clients/create.vue"),
        },
        {
            path: "guide/platform/:platform/clients/edit/:id",
            name: "panel-platforms-clients-edit",
            component: () => import("@/Pages/panel/platforms/clients/edit.vue"),
        },

        // support messages
        {
            path: "support/messages",
            name: "panel-messages-index",
            component: () => import("@/Pages/panel/messages/index.vue"),
        },

        {
            path: "support/messages/edit/:id",
            name: "panel-messages-edit",
            component: () => import("@/Pages/panel/messages/edit.vue"),
        },


        // users
        {
            path: "users",
            name: "panel-users-index",
            component: () => import("@/Pages/panel/users/index.vue"),
        },

        {
            path: "users/edit/:id",
            name: "panel-users-edit",
            component: () => import("@/Pages/panel/users/edit.vue"),
        },
        {
            path: "users/report/:id",
            name: "panel-users-report",
            component: () => import("@/Pages/panel/users/report.vue"),
        },


        {
            path: "users/create",
            name: "panel-users-create",
            component: () => import("@/Pages/panel/users/create.vue"),
        },

        // pricing
        {
            path: "pricing",
            name: "panel-pricing-index",
            component: () => import("@/Pages/panel/pricing/index.vue"),
        },
        {
            path: "pricing/create",
            name: "panel-pricing-create",
            component: () => import("@/Pages/panel/pricing/create.vue"),
        },
        {
            path: "pricing/edit/:id",
            name: "panel-pricing-edit",
            component: () => import("@/Pages/panel/pricing/edit.vue"),
        },
        // setting

        {
            path: "setting/variables",
            name: "panel-settings-index",
            component: () => import("@/Pages/panel/settings/variables.vue"),
        },

        // orders
        {
            path: "orders",
            name: "panel-orders-index",
            component: () => import("@/Pages/panel/orders/index.vue"),
        },
        {
            path: "orders/edit/:id",
            name: "panel-orders-edit",
            component: () => import("@/Pages/panel/orders/edit.vue"),
        },

        // subscriptions
        {
            path: "subscriptions",
            name: "panel-subscriptions-index",
            component: () => import("@/Pages/panel/subscriptions/index.vue"),
        },
        {
            path: "subscriptions/create",
            name: "panel-subscriptions-create",
            component: () => import("@/Pages/panel/subscriptions/create.vue"),
        },
        {
            path: "subscriptions/edit/:id",
            name: "panel-subscriptions-edit",
            component: () => import("@/Pages/panel/subscriptions/edit.vue"),
        },

    ],
};
