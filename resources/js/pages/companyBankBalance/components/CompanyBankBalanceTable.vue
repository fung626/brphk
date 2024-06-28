<template>
    <div>
        <Dialog ref="dialog" />
        <CRow class="p-2">
            <CCol sm="9">
                <CInput
                    size="sm"
                    v-model="searchText"
                    v-on:keyup.enter="search"
                >
                    <template #prepend>
                        <CButton color="primary" size="sm" v-on:click="search">
                            <CIcon name="cil-magnifying-glass" size="sm" />
                        </CButton>
                    </template>
                </CInput>
            </CCol>
            <CCol sm="3" class="text-right">
                <CButton
                    v-if="addVisible"
                    color="primary"
                    size="sm"
                    v-on:click="add"
                >
                    <CIcon name="cil-plus" size="sm" />
                </CButton>
                <CButton color="primary" size="sm" v-on:click="download">
                    <CIcon name="cil-cloud-download" size="sm" />
                </CButton>
                <CButton color="primary" size="sm" v-on:click="reload">
                    <CIcon name="cil-reload" size="sm" />
                </CButton>
            </CCol>
        </CRow>

        <v-data-table
            class="elevation-1"
            :page="page"
            :pageCount="pageCount"
            :headers="headers"
            :items="items"
            :options.sync="options"
            :server-items-length="serverItemsLength"
            :loading="loading"
            :sort-by.sync="sortBy"
            :sort-desc.sync="sortDesc"
            :footer-props="{
                disableItemsPerPage: disableItemsPerPage,
                disablePagination: disablePagination,
                showFirstLastPage: true,
                showCurrentPage: true,
                itemsPerPageOptions: [10, 20, 50, 100]
            }"
        >
            <template v-slot:[`item.name`]="{ item }">
                {{ item.name_tc }} {{ item.name_en }}
            </template>
            <template v-slot:[`item.created_at`]="{ item }">
                {{ item.created_at | moment("dddd, Do MMMM YYYY") }}
            </template>
            <template v-slot:[`item.updated_at`]="{ item }">
                {{ item.updated_at | moment("dddd, Do MMMM YYYY") }}
            </template>
            <template v-slot:[`item.actions`]="{ item }">
                <CButtonGroup>
                    <CButton
                        v-for="action in item.actions"
                        :key="action.key"
                        :color="action.color"
                        :disabled="action.disabled"
                        size="sm"
                        @click="click(item, action.type)"
                    >
                        {{ action.title }}
                    </CButton>
                </CButtonGroup>
            </template>
        </v-data-table>
    </div>
</template>
<script>
//
import { mapState } from "vuex";
import { Dialog } from "../../../components";

export default {
    name: "CompanyBankBalanceTable",
    components: {
        Dialog
    },
    props: {
        companyId: null,
        companyBankAccountId: null
    },
    computed: {
        ...mapState(["companyBankAccountBalance"]),
        serverItemsLength() {
            return this.companyBankAccountBalance.data?.total;
        },
        pageCount() {
            return this.companyBankAccountBalance.data?.last_page;
        },
        items() {
            return this.companyBankAccountBalance.data?.data;
        }
    },
    data() {
        return {
            addVisible: false,
            bankAccountData: {},
            searchText: null,
            page: 1,
            loading: false,
            options: {},
            sortBy: "created_at",
            sortDesc: false,
            disableItemsPerPage: false,
            disablePagination: false,
            headers: [
                { text: this.$t("user"), value: "user.name" },
                { text: this.$t("company"), value: "company.name_tc" },
                { text: this.$t("bank"), value: "bank_account.bank" },
                {
                    text: `${this.$t("account")}${this.$t("type")}`,
                    value: "bank_account.account_type"
                },
                { text: this.$t("balance"), value: "balance" },
                { text: this.$t("remark"), value: "remark" },
                { text: this.$t("createdat"), value: "created_at" },
                { text: this.$t("updatedat"), value: "updated_at" },
                { text: this.$t("actions"), value: "actions", sortable: false }
            ]
        };
    },
    watch: {
        options: {
            handler() {
                this.fetch();
            }
        },
        loading() {
            this.disableItemsPerPage = this.loading;
            this.disablePagination = this.loading;
        },
        searchText(val) {}
    },
    mounted() {
        this.updateAddVisible();
    },
    methods: {
        fetch() {
            let self = this;
            self.loading = true;
            const { page, itemsPerPage, sortBy, sortDesc } = self.options;
            let data = {
                company_id: self.$props.companyId,
                company_bank_account_id: self.$props.companyBankAccountId,
                page: page,
                per_page: itemsPerPage,
                sort_by: sortBy,
                sort_desc: sortDesc,
                search: self.searchText
            };
            this.$store
                .dispatch("company/bankaccount/balance/get", data)
                .then(response => {
                    // self.items = response.data.data;
                    // self.serverItemsLength = response.data.total;
                    // self.pageCount = response.data.last_page;
                    self.loading = false;
                })
                .catch(error => {
                    self.loading = false;
                });

            if (
                self.$props.companyBankAccountId &&
                self.$store.getters.isEmployee
            ) {
                this.$store
                    .dispatch("company/bankaccount/details", {
                        id: self.$props.companyBankAccountId
                    })
                    .then(response => {
                        // console.log(response.data.data);
                        self.bankAccountData = response.data.data;
                        self.updateAddVisible();
                    })
                    .catch(error => {});
            }
        },
        search() {
            this.fetch();
        },
        add() {
            this.$router.push({
                name: "CreateCompanyBankBalance",
                params: { id: this.$props.companyBankAccountId }
            });
        },
        download() {
            let self = this;
            self.loading = true;
            const { page, itemsPerPage, sortBy, sortDesc } = self.options;
            let data = {
                company_id: self.$props.companyId,
                company_bank_account_id: self.$props.companyBankAccountId,
                page: page,
                per_page: itemsPerPage,
                sort_by: sortBy,
                sort_desc: sortDesc,
                search: self.searchText
            };
            this.$store
                .dispatch("company/bankaccount/balance/export", data)
                .then(response => {
                    self.loading = false;
                })
                .catch(error => {
                    self.loading = false;
                });
        },
        reload() {
            this.fetch();
        },
        updateAddVisible() {
            this.addVisible =
                this.$props.companyBankAccountId &&
                (this.$store.getters.isAdmin ||
                    this.$store.getters.authUser.id ===
                        this.bankAccountData?.owner?.id);
        },
        async click(item, type) {
            switch (type) {
                case "RouterPush":
                    this.$router.push({
                        name: "CompanyBankDetails",
                        params: { id: item.id }
                    });
                    break;
                case "Delete":
                    if (
                        await this.$refs.dialog.open(
                            this.$t("alert.title"),
                            this.$t("alert.delete")
                        )
                    ) {
                        let self = this;
                        self.loading = true;
                        this.$store
                            .dispatch("company/bankaccount/balance/delete", {
                                id: item.id
                            })
                            .then(response => {
                                self.loading = false;
                                self.fetch();
                            })
                            .catch(error => {
                                self.loading = false;
                            });
                    }
                    break;
            }
            // console.log(id, key);
        }
    }
};
</script>
