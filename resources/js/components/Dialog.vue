<template>
    <v-dialog v-model="dialog" :max-width="options.width" @keydown.esc="cancel">
        <v-card>
            <v-toolbar dark :color="options.color" dense flat>
                <v-toolbar-title
                    class="text-body-2 font-weight-bold grey--text"
                >
                    {{ title }}
                </v-toolbar-title>
            </v-toolbar>
            <v-card-text
                v-show="!!message"
                class="pa-4 black--text"
                v-html="message"
            ></v-card-text>
            <v-card-actions class="pt-3">
                <v-spacer></v-spacer>
                <CButton @click="confirm" color="danger" class="px-4">
                    {{ $t("button.confirm") }}
                </CButton>
                <CButton
                    v-if="!options.noconfirm"
                    @click="cancel"
                    color="secondary"
                    class="px-4 ml-2"
                >
                    {{ $t("button.cancel") }}
                </CButton>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    name: "Dialog",
    data() {
        return {
            dialog: false,
            resolve: null,
            reject: null,
            message: null,
            title: null,
            options: {
                color: "grey lighten-3",
                width: 400,
                zIndex: 200,
                noconfirm: false
            }
        };
    },

    methods: {
        open(title, message, options) {
            this.dialog = true;
            this.title = title;
            this.message = message;
            this.options = Object.assign(this.options, options);
            return new Promise((resolve, reject) => {
                this.resolve = resolve;
                this.reject = reject;
            });
        },
        confirm() {
            this.resolve(true);
            this.dialog = false;
        },
        cancel() {
            this.resolve(false);
            this.dialog = false;
        }
    }
};
</script>
