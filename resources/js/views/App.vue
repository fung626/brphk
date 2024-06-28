<template>
    <router-view></router-view>
</template>

<script>
import { mapGetters } from "vuex";
// import { mapGetters } from "../../assets/scss/app";

export default {
    computed: mapGetters({
        authenticated: "authCheck"
    }),
    data() {
        return {
            drawer: true
        };
    }
};
</script>

<style lang="scss">
// Import Main styles for this application
@import "../../assets/scss/style";
</style>
