<template>
    <CCard class="p-4">
        <CCardBody>
            <form v-on:submit.prevent>
                <h4>{{ $t("create") }}{{ $t("expenses") }}</h4>
                <hr />
                <v-row>
                    <c-col md="6" sm="6">
                        <v-text-field
                            v-model="chequeBank"
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
                            v-model="chequeNo"
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
                            v-model="chequeIssuer"
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
                            v-model="signer"
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
                    v-model="issuedCompany"
                    :label="$t('cheque.issued.company')"
                    :error="errors.issued_company ? true : false"
                    :error-messages="errors.issued_company"
                    required
                    outlined
                    dense
                    clearable
                ></v-text-field>
                <v-text-field
                    v-model="payTo"
                    :label="$t('cheque.payto')"
                    :error="errors.pay_to ? true : false"
                    :error-messages="errors.pay_to"
                    required
                    outlined
                    dense
                    clearable
                ></v-text-field>
                <v-text-field
                    v-model="amount"
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
                                    v-model="chequeIssuedDate"
                                    :label="$t('cheque.issued.date')"
                                    outlined
                                    dense
                                    clearable
                                    readonly
                                    :error="
                                        errors.cheque_issued_date ? true : false
                                    "
                                    :error-messages="errors.cheque_issued_date"
                                    v-bind="attrs"
                                    v-on="on"
                                ></v-text-field>
                            </template>
                            <v-date-picker
                                v-model="chequeIssuedDate"
                                @input="chequeIssuedDateMenu = false"
                            ></v-date-picker>
                        </v-menu>
                    </c-col>
                    <c-col md="6" sm="6">
                        <p>{{ $t("internaltransfer") }}</p>
                        <v-radio-group v-model="internalTransfer" row>
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
                    v-model="item"
                    :label="$t('item')"
                    :error="errors.item ? true : false"
                    :error-messages="errors.item"
                    required
                    outlined
                    dense
                    clearable
                ></v-text-field>
                <v-text-field
                    v-model="remark"
                    :label="$t('remark')"
                    :error="errors.remark ? true : false"
                    :error-messages="errors.remark"
                    required
                    outlined
                    dense
                    clearable
                ></v-text-field>
                <hr />
                <CButton @click="submit" color="primary" class="px-4">
                    {{ $t("button.submit") }}
                    <v-progress-circular
                        v-if="loading"
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
export default {
    name: "CreateExpenses",
    components: {},
    data() {
        return {
            chequeBank: "",
            chequeNo: "",
            chequeIssuer: "",
            signer: "",
            issuedCompany: "",
            payTo: "",
            amount: "",
            chequeIssuedDate: "",
            chequeIssuedDateMenu: false,
            internalTransfer: 0,
            item: "",
            remark: "",
            loading: false,
            errors: {}
        };
    },
    methods: {
        submit() {
            let self = this;
            if (self.loading) {
                return;
            }
            self.loading = true;
            let data = {
                cheque_bank: self.chequeBank,
                cheque_no: self.chequeNo,
                cheque_issuer: self.chequeIssuer,
                signer: self.signer,
                issued_company: self.issuedCompany,
                pay_to: self.payTo,
                amount: self.amount,
                cheque_issued_date: self.chequeIssuedDate,
                internal_transfer: self.internalTransfer,
                item: self.item,
                remark: self.remark
            };
            this.$store
                .dispatch("expenses/create", data)
                .then(() => {
                    self.loading = false;
                    self.errors = {};
                    self.$router.back();
                })
                .catch(error => {
                    self.loading = false;
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
