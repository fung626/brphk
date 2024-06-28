<template>
    <v-snackbar v-model="show" :timeout="timeout" :vertical="true" multi-line>
        {{ text }}
        <template>
            <v-btn dark text @click="close">
                {{ $t("button.close") }}
            </v-btn>
        </template>
    </v-snackbar>
</template>

<script>
import { mapState } from "vuex";

export default {
    name: "Snackbar",
    data() {
        return {
            timeout: 60000
        };
    },
    computed: {
        ...mapState(["uisnackbar"]),
        show: {
            get() {
                return this.uisnackbar.show;
            },
            set(value) {
                if (!value) {
                    this.$store.dispatch("snackbar/close");
                }
            }
        },
        color() {
            return this.uisnackbar.color;
        },
        text() {
            return this.uisnackbar.text;
        }
    },
    methods: {
        close() {
            this.$store.dispatch("snackbar/close");
        }
    }
};
</script>
