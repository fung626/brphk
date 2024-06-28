<template>
    <CCard>
        <v-progress-linear
            :active="fetchLoading"
            indeterminate
            color="cyan"
        ></v-progress-linear>
        <CCardBody>
            <CCard class="p-4 border-0">
                <CCardBody>
                    <form v-on:submit.prevent>
                        <h4>{{ $t("info") }}</h4>
                        <hr />
                        <v-text-field
                            v-model="formData.company"
                            :label="$t('company')"
                            :error="errors.company ? true : false"
                            :error-messages="errors.company"
                            required
                            outlined
                            dense
                            clearable
                        ></v-text-field>
                        <v-text-field
                            v-model="formData.item"
                            :label="$t('item')"
                            :error="errors.item ? true : false"
                            :error-messages="errors.item"
                            required
                            outlined
                            dense
                            clearable
                        ></v-text-field>
                        <v-text-field
                            v-model="formData.amount"
                            :label="$t('amount')"
                            type="number"
                            :error="errors.amount ? true : false"
                            :error-messages="errors.amount"
                            required
                            outlined
                            dense
                            clearable
                        ></v-text-field>
                        <v-menu
                            v-model="dateMenu"
                            :close-on-content-click="false"
                            :nudge-right="40"
                            transition="scale-transition"
                            offset-y
                            min-width="auto"
                        >
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                    v-model="formData.date"
                                    :label="$t('date')"
                                    outlined
                                    dense
                                    clearable
                                    readonly
                                    v-bind="attrs"
                                    v-on="on"
                                ></v-text-field>
                            </template>
                            <v-date-picker
                                v-model="formData.date"
                                @input="dateMenu = false"
                            ></v-date-picker>
                        </v-menu>
                        <v-text-field
                            v-model="formData.remark"
                            :label="$t('remark')"
                            :error="errors.remark ? true : false"
                            :error-messages="errors.remark"
                            required
                            outlined
                            dense
                            clearable
                        ></v-text-field>
                        <hr />
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
        </CCardBody>
    </CCard>
</template>

<script>
export default {
    name: "TaxDetails",
    components: {},
    data() {
        return {
            formData: {},
            dateMenu: false,
            fetchLoading: false,
            updateLoading: false,
            errors: {}
        };
    },
    mounted() {
        this.fetch();
    },
    methods: {
        fetch() {
            let self = this;
            self.fetchLoading = true;
            let data = {
                id: self.$route.params.id
            };
            this.$store
                .dispatch("tax/details", data)
                .then(response => {
                    self.formData = response.data.data;
                    self.fetchLoading = false;
                })
                .catch(error => {
                    self.fetchLoading = false;
                    self.$router.push({ name: "Dashboard" });
                    self.$store.dispatch("snackbar/show", {
                        text: self.$t("error.nodata")
                    });
                });
        },
        update() {
            let self = this;
            if (self.updateLoading) {
                return;
            }
            self.updateLoading = true;
            this.$store
                .dispatch("tax/update", self.formData)
                .then(() => {
                    self.updateLoading = false;
                    self.errors = {};
                })
                .catch(error => {
                    self.updateLoading = false;
                    self.errors = error.response.data?.data;
                });
        }
    }
};
</script>

<style scoped>
.v-text-field >>> input {
    font-size: 0.8em;
    font-weight: 100;
}
.v-text-field >>> label {
    font-size: 0.8em;
}
.v-text-field >>> button {
    font-size: 0.8em;
}
</style>
