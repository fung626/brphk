<template>
    <div class="c-app">
        <Snackbar />
        <TheSidebar />
        <CWrapper>
            <TheHeader />
            <div class="c-body">
                <main class="c-main">
                    <CContainer fluid>
                        <v-app light>
                            <transition name="fade">
                                <router-view></router-view>
                            </transition>
                        </v-app>
                    </CContainer>
                </main>
                <TheFooter />
            </div>
        </CWrapper>
    </div>
</template>

<script>
//
import TheSidebar from "./TheSidebar";
import TheHeader from "./TheHeader";
import TheFooter from "./TheFooter";
import { Snackbar } from "../components";

export default {
    name: "TheContainer",
    components: {
        TheSidebar,
        TheHeader,
        TheFooter,
        Snackbar
    }
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s;
}
.fade-enter,
.fade-leave-to {
    opacity: 0;
}
</style>
