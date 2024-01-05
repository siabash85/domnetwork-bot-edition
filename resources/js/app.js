import './bootstrap';
import '../css/app.css';
import '../css/master.css';
import { createApp, h } from 'vue';
import router from "./Router";
import ApiService from "@/Core/services/ApiService";
import { createPinia } from "pinia";

import App from "./Layouts/App.vue";
import '@mdi/font/css/materialdesignicons.css'

import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import { initVeeValidate } from "@/Core/Plugins/vee-validate";


const vuetify = createVuetify({
    components,
    directives,
    locale: {
        locale: 'fa',
        fallback: 'fa',

        rtl: { fa: true, customLocale: true, },
    },
})
const app = createApp(App);

app.config.globalProperties.$filters = {

    separate(Number) {
        // const value = Number / 10
        const value = Number
        return value.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
    }
};
ApiService.init(app);
initVeeValidate(app)
const init = app.use(vuetify).use(createPinia()).use(router);

init.mount("#app");
