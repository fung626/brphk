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
                <CButton color="primary" size="sm" v-on:click="add">
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
            <template v-slot:[`item.cheque_issued_date`]="{ item }">
                <div v-if="item.cheque_issued_date">
                    {{ item.cheque_issued_date | moment("dddd, Do MMMM YYYY") }}
                </div>
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
                        @click="click(item.id, action.type)"
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
import { Dialog } from "../../components";

export default {
    name: "Expenses",
    components: {
        Dialog
    },
    computed: {
        ...mapState(["expenses"]),
        serverItemsLength() {
            return this.expenses.data?.total;
        },
        pageCount() {
            return this.expenses.data?.last_page;
        },
        items() {
            return this.expenses.data?.data;
        }
    },
    props: {
        id: null
    },
    data() {
        return {
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
                { text: this.$t("cheque.bank"), value: "cheque_bank" },
                { text: this.$t("cheque.no"), value: "cheque_no" },
                { text: this.$t("cheque.issuer"), value: "cheque_issuer" },
                { text: this.$t("cheque.signer"), value: "signer" },
                { text: this.$t("amount"), value: "amount" },
                {
                    text: this.$t("cheque.issued.company"),
                    value: "issued_company"
                },
                { text: this.$t("cheque.payto"), value: "pay_to" },
                {
                    text: this.$t("cheque.issued.date"),
                    value: "cheque_issued_date"
                },
                {
                    text: this.$t("internaltransfer"),
                    value: "internal_transfer"
                },
                { text: this.$t("item"), value: "item" },
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
        this.fetch();
    },
    methods: {
        fetch() {
            let self = this;
            self.loading = true;
            const { page, itemsPerPage, sortBy, sortDesc } = self.options;
            let data = {
                rent_id: self.$props.id,
                page: page,
                per_page: itemsPerPage,
                sort_by: sortBy,
                sort_desc: sortDesc,
                search: self.searchText
            };
            this.$store
                .dispatch("expenses/get", data)
                .then(response => {
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
            this.$router.push({ name: "CreateExpenses" });
        },
        download() {
            let self = this;
            self.loading = true;
            const { page, itemsPerPage, sortBy, sortDesc } = self.options;
            let data = {
                rent_id: self.$props.id,
                page: page,
                per_page: itemsPerPage,
                sort_by: sortBy,
                sort_desc: sortDesc,
                search: self.searchText
            };
            this.$store
                .dispatch("expenses/export", data)
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
        async click(id, type) {
            switch (type) {
                case "RouterPush":
                    this.$router.push({
                        name: "ExpensesDetails",
                        params: { id: id }
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
                            .dispatch("expenses/delete", { id: id })
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
        }
    }
};
</script>
