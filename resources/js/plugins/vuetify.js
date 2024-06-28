import "@mdi/font/css/materialdesignicons.css";
import Vue from "vue";
import Vuetify, {
    VApp,
    VAppBar,
    VContainer,
    VContent,
    VIcon,
    VNavigationDrawer,
    VParallax,
    VSnackbar,
    VToolbar
} from "vuetify/lib";

Vue.use(Vuetify, {
    icons: {
        iconfont: "md"
    },
    component: {
        VApp,
        VAppBar,
        VContainer,
        VContent,
        VIcon,
        VNavigationDrawer,
        VParallax,
        VSnackbar,
        VToolbar
    }
});

const opts = {
    theme: {
        dark: false
    }
};

export default new Vuetify(opts);
