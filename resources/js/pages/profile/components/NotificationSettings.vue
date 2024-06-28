<template>
    <CCard class="border-0">
        <v-progress-linear
            :active="fetchLoading"
            indeterminate
            color="cyan"
        ></v-progress-linear>
        <CCardBody>
            <form v-on:submit.prevent>
                <v-switch
                    v-if="$store.getters.isAdmin"
                    class="py-4"
                    v-model="summary"
                    inset
                    :label="
                        `${$t('summary')}${$t('email')}${$t('notification')}`
                    "
                ></v-switch>
                <CButton @click="update" color="primary" class="px-4">
                    {{ $t("button.update") }}
                    <v-progress-circular
                        v-if="updateLoading"
                        indeterminate
                        color="primary"
                        :size="15"
                    ></v-progress-circular>
                </CButton>
            </form>
        </CCardBody>
    </CCard>
</template>

<script>
import { mapState } from "vuex";

export default {
    name: "NotificationSettings",
    computed: {
        ...mapState(["profile/notification"])
    },
    data() {
        return {
            summary: false,
            fetchLoading: false,
            updateLoading: false
        };
    },
    mounted() {
        this.fetch();
    },
    methods: {
        fetch() {
            let self = this;
            if (self.fetchLoading) {
                return;
            }
            self.fetchLoading = true;
            this.$store
                .dispatch("profile/notification/get", {})
                .then(response => {
                    let res = response.data;
                    self.summary = res.summary ? res.summary : false;
                    self.fetchLoading = false;
                })
                .catch(error => {
                    self.fetchLoading = false;
                });
        },
        update() {
            let self = this;
            if (self.updateLoading) {
                return;
            }
            self.updateLoading = true;
            let data = {
                summary: self.summary
            };
            this.$store
                .dispatch("profile/notification/update", data)
                .then(response => {
                    let res = response.data;
                    self.$store.dispatch("snackbar/show", {
                        text: "Settings Has Been Successfully Reset"
                    });
                    self.summary = res.summary ? res.summary : false;
                    self.updateLoading = false;
                })
                .catch(error => {
                    self.$store.dispatch("snackbar/show", {
                        text: error.response.data.msg
                    });
                    self.updateLoading = false;
                });
        }
    }
};
</script>
