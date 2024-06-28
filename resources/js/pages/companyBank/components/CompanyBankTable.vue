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
                    color="primary"
                    size="sm"
                    v-on:click="add"
                    v-if="$store.getters.isAdmin && id"
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
            <template
                v-slot:[`item.company_bank_account.created_at`]="{ item }"
            >
                {{
                    item.company_bank_account.created_at
                        | moment("dddd, Do MMMM YYYY")
                }}
            </template>
            <template
                v-slot:[`item.company_bank_account.updated_at`]="{ item }"
            >
                {{
                    item.company_bank_account.updated_at
                        | moment("dddd, Do MMMM YYYY")
                }}
            </template>
            <template v-slot:[`item.actions`]="{ item }">
                <CButtonGroup>
                    <CButton
                        v-for="action in item.actions"
                        :key="action.key"
                        :color="action.color"
                        :disabled="action.disabled"
                        size="sm"
                        @click="click(item, action)"
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
    name: "CompanyBankTable",
    components: {
        Dialog
    },
    props: {
        id: null
    },
    computed: {
        ...mapState(["companyBankAccount"]),
        serverItemsLength() {
            return this.companyBankAccount.data?.total;
        },
        pageCount() {
            return this.companyBankAccount.data?.last_page;
        },
        items() {
            return this.companyBankAccount.data?.data;
        }
    },
    data() {
        return {
            filters: {},
            activeFilters: {},
            searchText: null,
            page: 1,
            // serverItemsLength: 0,
            // pageCount: 0,
            // items: [],
            loading: false,
            options: {},
            sortBy: "company_bank_account.created_at",
            sortDesc: false,
            disableItemsPerPage: false,
            disablePagination: false,
            headers: [
                { text: this.$t("owner"), value: "owner.name" },
                { text: this.$t("company"), value: "company.name_tc" },
                { text: this.$t("bank"), value: "bank" },
                {
                    text: `${this.$t("account")}${this.$t("type")}`,
                    value: "account_type"
                },
                {
                    text: this.$t("createdat"),
                    value: "company_bank_account.created_at"
                },
                {
                    text: this.$t("updatedat"),
                    value: "company_bank_account.updated_at"
                },
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
    methods: {
        fetch() {
            let self = this;
            self.loading = true;
            const { page, itemsPerPage, sortBy, sortDesc } = self.options;
            let data = {
                company_id: self.$props.id,
                page: page,
                per_page: itemsPerPage,
                sort_by: sortBy,
                sort_desc: sortDesc,
                search: self.searchText
            };
            this.$store
                .dispatch("company/bankaccount/get", data)
                .then(response => {
                    // self.items = response.data.data;
                    // self.serverItemsLength = response.data.total;
                    // self.pageCount = response.data.last_page;
                    self.loading = false;
                })
                .catch(error => {
                    self.loading = false;
                });
        },
        search() {
            this.fetch();
        },
        add() {
            this.$router.push({
                name: "CreateCompanyBank",
                params: { id: this.$props.id }
            });
        },
        download() {
            let self = this;
            self.loading = true;
            const { page, itemsPerPage, sortBy, sortDesc } = self.options;
            let data = {
                company_id: self.$props.id,
                page: page,
                per_page: itemsPerPage,
                sort_by: sortBy,
                sort_desc: sortDesc,
                search: self.searchText
            };
            this.$store
                .dispatch("company/bankaccount/export", data)
                .then(() => {
                    self.loading = false;
                })
                .catch(error => {
                    self.loading = false;
                });
        },
        reload() {
            this.fetch();
        },
        async click(item, action) {
            let type = action.type;
            switch (type) {
                case "RouterPush":
                    let route = action.route;
                    this.$router.push({
                        name: route,
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
                            .dispatch("company/bankaccount/delete", {
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
