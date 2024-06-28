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
                        <v-row>
                            <c-col md="6" sm="6">
                                <v-text-field
                                    v-model="formData.cheque_bank"
                                    :label="$t('cheque.bank')"
                                    :error="errors.cheque_bank ? true : false"
                                    :error-messages="errors.cheque_bank"
                                    required
                                    outlined
                                    dense
                                    clearable
                                ></v-text-field>
                            </c-col>
                            <c-col md="6" sm="6">
                                <v-text-field
                                    v-model="formData.cheque_no"
                                    :label="$t('cheque.no')"
                                    :error="errors.cheque_no ? true : false"
                                    :error-messages="errors.cheque_no"
                                    required
                                    outlined
                                    dense
                                    clearable
                                ></v-text-field>
                            </c-col>
                        </v-row>
                        <v-row>
                            <c-col md="6" sm="6">
                                <v-text-field
                                    v-model="formData.cheque_issuer"
                                    :label="$t('cheque.issuer')"
                                    :error="errors.cheque_issuer ? true : false"
                                    :error-messages="errors.cheque_issuer"
                                    required
                                    outlined
                                    dense
                                    clearable
                                ></v-text-field>
                            </c-col>
                            <c-col md="6" sm="6">
                                <v-text-field
                                    v-model="formData.signer"
                                    :label="$t('cheque.signer')"
                                    :error="errors.signer ? true : false"
                                    :error-messages="errors.signer"
                                    required
                                    outlined
                                    dense
                                    clearable
                                ></v-text-field>
                            </c-col>
                        </v-row>
                        <v-text-field
                            v-model="formData.issued_company"
                            :label="$t('cheque.issued.company')"
                            :error="errors.issued_company ? true : false"
                            :error-messages="errors.issued_company"
                            required
                            outlined
                            dense
                            clearable
                        ></v-text-field>
                        <v-text-field
                            v-model="formData.pay_to"
                            :label="$t('cheque.payto')"
                            :error="errors.pay_to ? true : false"
                            :error-messages="errors.pay_to"
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
                        <v-row>
                            <c-col md="6" sm="6">
                                <v-menu
                                    v-model="chequeIssuedDateMenu"
                                    :close-on-content-click="false"
                                    :nudge-right="40"
                                    transition="scale-transition"
                                    offset-y
                                    min-width="auto"
                                >
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-text-field
                                            v-model="
                                                formData.cheque_issued_date
                                            "
                                            :label="$t('cheque.issued.date')"
                                            outlined
                                            dense
                                            clearable
                                            readonly
                                            :error="
                                                errors.cheque_issued_date
                                                    ? true
                                                    : false
                                            "
                                            :error-messages="
                                                errors.cheque_issued_date
                                            "
                                            v-bind="attrs"
                                            v-on="on"
                                        ></v-text-field>
                                    </template>
                                    <v-date-picker
                                        v-model="formData.cheque_issued_date"
                                        @input="chequeIssuedDateMenu = false"
                                    ></v-date-picker>
                                </v-menu>
                            </c-col>
                            <c-col md="6" sm="6">
                                <p>{{ $t("internaltransfer") }}</p>
                                <v-radio-group
                                    v-model="formData.internal_transfer"
                                    row
                                >
                                    <v-radio
                                        :label="$t('radio.yes')"
                                        v-bind:value="1"
                                    ></v-radio>
                                    <v-radio
                                        :label="$t('radio.no')"
                                        v-bind:value="0"
                                    ></v-radio>
                                </v-radio-group>
                            </c-col>
                        </v-row>
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
    name: "ExpensesDetails",
    components: {},
    data() {
        return {
            formData: {},
            chequeIssuedDateMenu: false,
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
                .dispatch("expenses/details", data)
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
                .dispatch("expenses/update", self.formData)
                .then(response => {
                    self.formData = response.data.data;
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
