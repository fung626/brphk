<template>
    <CCard class="p-4">
        <CCardBody>
            <form v-on:submit.prevent>
                <h4>{{ $t("create") }}{{ $t("bank") }}</h4>
                <hr />
                <v-autocomplete
                    v-if="!$route.params.id"
                    v-model="company"
                    :items="companies.items"
                    :loading="companies.loading"
                    :search-input.sync="companies.search"
                    required
                    outlined
                    dense
                    hide-no-data
                    hide-selected
                    item-text="name_tc"
                    item-value="id"
                    :label="$t('company')"
                    return-object
                ></v-autocomplete>
                <v-autocomplete
                    v-model="owner"
                    :items="users.items"
                    :loading="users.loading"
                    :search-input.sync="users.search"
                    required
                    outlined
                    dense
                    hide-no-data
                    hide-selected
                    item-text="name"
                    item-value="id"
                    :label="$t('owner')"
                    return-object
                ></v-autocomplete>
                <v-text-field
                    v-model="bank"
                    :label="$t('bank')"
                    :error="errors.bank ? true : false"
                    :error-messages="errors.bank"
                    required
                    outlined
                    dense
                    clearable
                ></v-text-field>
                <v-text-field
                    v-model="accountType"
                    :label="$t('type')"
                    :error="errors.account_type ? true : false"
                    :error-messages="errors.account_type"
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
    name: "CreateCompanyBank",
    props: {},
    components: {},
    data() {
        return {
            company: {},
            owner: {},
            bank: "",
            accountType: "",
            companies: {
                items: [],
                loading: false
            },
            users: {
                items: [],
                loading: false
            },
            loading: false,
            errors: {}
        };
    },
    watch: {
        companies: [
            function search(val) {
                let self = this;
                if (self.companies.length > 0 || self.companies.loading) return;
                self.companies.loading = true;
                this.$store
                    .dispatch("company/get", {})
                    .then(response => {
                        self.companies.items = response.data;
                        self.companies.loading = false;
                    })
                    .catch(error => {
                        self.companies.loading = false;
                    });
            }
        ],
        users: [
            function search(val) {
                let self = this;
                if (self.users.length > 0 || self.users.loading) return;
                self.users.loading = true;
                this.$store
                    .dispatch("user/get", {})
                    .then(response => {
                        self.users.items = response.data;
                        self.users.loading = false;
                    })
                    .catch(error => {
                        self.companies.loading = false;
                    });
            }
        ]
    },
    methods: {
        submit() {
            let self = this;
            if (self.loading) {
                return;
            }
            self.loading = true;
            let data = {
                company: self.company,
                owner: self.owner,
                company_id: self.$route.params.id,
                bank: self.bank,
                account_type: self.accountType
            };
            this.$store
                .dispatch("company/bankaccount/create", data)
                .then(response => {
                    self.loading = false;
                    self.errors = {};
                    self.$router.back();
                })
                .catch(error => {
                    self.errors = error.response.data?.data;
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
