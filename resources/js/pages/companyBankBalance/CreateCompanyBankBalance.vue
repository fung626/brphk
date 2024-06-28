<template>
    <CCard class="p-4">
        <CCardBody>
            <form v-on:submit.prevent>
                <h4>{{ $t("create") }}{{ $t("bank") }}{{ $t("balance") }}</h4>
                <hr />
                <v-text-field
                    v-model="balance"
                    :label="$t('balance')"
                    type="number"
                    required
                    outlined
                    dense
                    clearable
                ></v-text-field>
                <v-text-field
                    v-model="remark"
                    :label="$t('remark')"
                    required
                    outlined
                    dense
                    clearable
                ></v-text-field>
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
    name: "CreateCompanyBankBalance",
    components: {},
    data() {
        return {
            balance: "",
            remark: "",
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
                company_bank_account_id: self.$route.params.id,
                balance: self.balance,
                remark: self.remark
            };
            this.$store
                .dispatch("company/bankaccount/balance/create", data)
                .then(response => {
                    self.loading = false;
                    self.$router.back();
                })
                .catch(error => {
                    self.loading = false;
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
