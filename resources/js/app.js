/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
// import "./libs";
import CoreuiVue from "@coreui/vue";
import Vue from "vue";
import { iconsSet as icons } from "../assets/icons/icons.js";
import "./bootstrap";
import "./components";
import "./plugins";
import { i18n } from "./plugins";
import vuetify from "./plugins/vuetify";
import router from "./router";
import store from "./store";
// import "../stylus/app.styl";
import "./utils";
import App from "./views/App";

/**
 * Next, we will create a fresh React component instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// window.Vue = require("vue");

// export default new Vuetify({});
Vue.config.performance = true;
Vue.use(CoreuiVue);

const app = new Vue({
    el: "#app",
    i18n,
    vuetify,
    store,
    icons,
    router,
    render: h => h(App)
});
