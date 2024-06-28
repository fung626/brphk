<template>
    <div>
        <template v-for="(alert, i) in alerts">
            <div v-bind:key="i" v-if="alert">
                <CAlert
                    :color="alert.color"
                    closeButton
                    :show="true"
                    @update:show="value => onAlertUpdate(value, alert)"
                >
                    {{ alert.message }}
                </CAlert>
            </div>
        </template>
    </div>
</template>

<script>
import { mapState } from "vuex";

export default {
    name: "TheAlert",
    data() {
        return {};
    },
    computed: {
        ...mapState(["uialert"]),
        alerts() {
            return this.uialert.alerts;
        }
    },
    methods: {
        onAlertUpdate(value, alert) {
            if (value === false) {
                this.$store.dispatch("removeAlert", alert);
            }
        }
    }
};
</script>
