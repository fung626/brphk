<template>
    <CCard class="p-4">
        <CCardBody>
            <form v-on:submit.prevent>
                <h4>
                    {{ $t("create") }}{{ $t("rent.title") }}{{ $t("payment") }}
                </h4>
                <hr />
                <v-text-field
                    v-model="amount"
                    :label="$t('rent.amount')"
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
                            v-model="paymentDateMenu"
                            :close-on-content-click="false"
                            :nudge-right="40"
                            transition="scale-transition"
                            offset-y
                            min-width="auto"
                        >
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                    v-model="paymentDate"
                                    :label="$t('date')"
                                    outlined
                                    dense
                                    clearable
                                    readonly
                                    v-bind="attrs"
                                    v-on="on"
                                    :error="errors.payment_date ? true : false"
                                    :error-messages="errors.payment_date"
                                ></v-text-field>
                            </template>
                            <v-date-picker
                                v-model="paymentDate"
                                @input="paymentDateMenu = false"
                            ></v-date-picker>
                        </v-menu>
                    </c-col>
                    <c-col md="6" sm="6">
                        <v-menu
                            v-model="paymentMonthMenu"
                            :close-on-content-click="false"
                            :nudge-right="40"
                            transition="scale-transition"
                            offset-y
                            min-width="auto"
                        >
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                    v-model="paymentMonth"
                                    :label="$t('rent.month')"
                                    outlined
                                    dense
                                    clearable
                                    readonly
                                    :error="errors.payment_month ? true : false"
                                    :error-messages="errors.payment_month"
                                    v-bind="attrs"
                                    v-on="on"
                                ></v-text-field>
                            </template>
                            <v-date-picker
                                v-model="paymentMonth"
                                @input="paymentMonthMenu = false"
                                type="month"
                            ></v-date-picker>
                        </v-menu>
                    </c-col>
                </v-row>

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
//

export default {
    name: "CreateRentPayment",
    components: {},
    props: {
        id: null
    },
    data() {
        return {
            amount: "",
            paymentDate: "",
            paymentDateMenu: false,
            paymentMonth: "",
            paymentMonthMenu: false,
            remark: "",
            errors: {},
            loading: false
        };
    },
    methods: {
        submit() {
            let self = this;
            if (self.loading) {
                return;
            }
            let data = {
                id: self.$route.params.id,
                amount: self.amount,
                payment_date: self.paymentDate,
                payment_month: self.paymentMonth,
                remark: self.remark
            };
            self.loading = true;
            this.$store
                .dispatch("rent/payment/create", data)
                .then(response => {
                    self.loading = false;
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
