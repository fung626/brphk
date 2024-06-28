<template>
    <CCard class="p-4">
        <CCardBody>
            <form v-on:submit.prevent>
                <h4>{{ $t("create") }}{{ $t("profit") }}</h4>
                <hr />
                <v-select
                    v-model="years"
                    :items="yearOptions"
                    :label="$t('years')"
                    required
                    outlined
                    dense
                    :error="errors.years ? true : false"
                    :error-messages="errors.years"
                ></v-select>
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
                <v-text-field
                    v-model="tax"
                    :label="$t('tax')"
                    type="number"
                    :error="errors.tax ? true : false"
                    :error-messages="errors.tax"
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
//
import { years } from "../../constants";
export default {
    name: "CreateProfit",
    computed: {
        yearOptions() {
            return years();
        }
    },
    data() {
        return {
            years: null,
            amount: null,
            tax: null,
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
            self.loading = true;
            let data = {
                company_id: self.$route.params.id,
                years: self.years,
                amount: self.amount,
                tax: self.tax,
                remark: self.remark
            };
            this.$store
                .dispatch("profit/create", data)
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
