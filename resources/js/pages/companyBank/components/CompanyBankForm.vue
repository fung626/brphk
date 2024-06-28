<template>
    <CCard class="border-0">
        <v-progress-linear
            :active="fetchLoading"
            indeterminate
            color="cyan"
        ></v-progress-linear>
        <CCardBody>
            <form v-on:submit.prevent>
                <v-autocomplete
                    v-if="$store.getters.isAdmin"
                    v-model="formData.owner"
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
                    :disabled="!$store.getters.isAdmin"
                ></v-autocomplete>
                <v-text-field
                    v-model="formData.bank"
                    :label="$t('bank')"
                    required
                    outlined
                    dense
                    clearable
                    :disabled="!$store.getters.isAdmin"
                ></v-text-field>
                <v-text-field
                    v-model="formData.account_type"
                    :label="`${$t('account')}${$t('type')}`"
                    required
                    outlined
                    dense
                    clearable
                    :disabled="!$store.getters.isAdmin"
                ></v-text-field>
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
//
export default {
    name: "CompanyBankForm",
    components: {},
    props: {
        id: null
    },
    data() {
        return {
            formData: {},
            users: {
                items: [],
                loading: false
            },
            fetchLoading: false,
            updateLoading: false
        };
    },
    watch: {
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
                        self.loading = false;
                    });
            }
        ]
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
            let data = {
                id: self.$route.params.id
            };
            this.$store
                .dispatch("company/bankaccount/details", data)
                .then(response => {
                    self.fetchLoading = false;
                    self.formData = response.data.data;
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
            let data = {
                id: self.$route.params.id,
                owner: self.formData.owner,
                bank: self.formData.bank,
                account_type: self.formData.account_type
            };
            this.$store
                .dispatch("company/bankaccount/update", data)
                .then(response => {
                    self.updateLoading = false;
                })
                .catch(error => {
                    self.updateLoading = false;
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
