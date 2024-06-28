<template>
    <div>
        <Dialog ref="dialog" />
        <div class="py-4">
            <h4>{{ $t("payable") }}{{ $t("table") }}</h4>
            <CRow class="p-2">
                <CCol sm="9">
                    <CInput
                        size="sm"
                        v-model="searchText"
                        v-on:keyup.enter="search"
                    >
                        <template #prepend>
                            <CButton
                                color="primary"
                                size="sm"
                                v-on:click="search"
                            >
                                <CIcon name="cil-magnifying-glass" size="sm" />
                            </CButton>
                        </template>
                    </CInput>
                </CCol>
                <CCol sm="3" class="text-right">
                    <CButton
                        color="primary"
                        size="sm"
                        v-if="$store.getters.isAdmin"
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
                <template v-slot:[`item.debt_date`]="{ item }">
                    <div v-if="item.debt_date">
                        {{ item.debt_date | moment("dddd, Do MMMM YYYY") }}
                    </div>
                </template>
                <template v-slot:[`item.due_date`]="{ item }">
                    <div v-if="item.due_date">
                        {{ item.due_date | moment("dddd, Do MMMM YYYY") }}
                    </div>
                </template>
                <template v-slot:[`item.paid_date`]="{ item }">
                    <div v-if="item.paid_date">
                        {{ item.paid_date | moment("dddd, Do MMMM YYYY") }}
                    </div>
                </template>
                <template v-slot:[`item.debt.created_at`]="{ item }">
                    {{ item.debt.created_at | moment("dddd, Do MMMM YYYY") }}
                </template>
                <template v-slot:[`item.debt.updated_at`]="{ item }">
                    {{ item.debt.updated_at | moment("dddd, Do MMMM YYYY") }}
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
                <template slot="body.append">
                    <tr v-if="!isMobile">
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>{{ $t("payable") }}{{ $t("amount") }}</th>
                        <th>{{ debtAmount }}</th>
                        <th>－{{ paidAmount }}</th>
                        <th></th>
                    </tr>
                    <tr v-if="!isMobile">
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>{{ $t("payable") }}{{ $t("total") }}</th>
                        <th>{{ totalAmount }}</th>
                        <th></th>
                        <th></th>
                    </tr>
                </template>
            </v-data-table>
        </div>
    </div>
</template>

<script>
//
import { isMobile } from "mobile-device-detect";
import { Dialog } from "../../../components";

export default {
    name: "PayableTable",
    components: {
        Dialog
    },
    data() {
        return {
            searchText: null,
            page: 1,
            loading: false,
            options: {},
            serverItemsLength: 0,
            pageCount: 0,
            items: [],
            sortBy: "debt.created_at",
            sortDesc: false,
            disableItemsPerPage: false,
            disablePagination: false,
            headers: [
                { text: this.$t("user"), value: "users.name" },
                { text: this.$t("item"), value: "item" },
                { text: this.$t("amount"), value: "amount" },
                { text: this.$t("paid"), value: "paid" },
                {
                    text: `${this.$t("payable")}${this.$t("date")}`,
                    value: "debt_date"
                },
                { text: this.$t("duedate"), value: "due_date" },
                { text: this.$t("paiddate"), value: "paid_date" },
                { text: this.$t("updatedby"), value: "updated_by.name" },
                { text: this.$t("createdat"), value: "debt.created_at" },
                { text: this.$t("updatedat"), value: "debt.updated_at" },
                { text: this.$t("actions"), value: "actions", sortable: false }
            ],
            debtAmount: 0,
            paidAmount: 0,
            totalAmount: 0,
            isMobile: isMobile
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
                type: "Payable",
                page: page,
                per_page: itemsPerPage,
                sort_by: sortBy,
                sort_desc: sortDesc,
                search: self.searchText
            };
            this.$store
                .dispatch("debt/get", data)
                .then(response => {
                    self.loading = false;
                    self.serverItemsLength = response.data.total;
                    self.pageCount = response.data.last_page;
                    self.items = response.data.data;
                    self.debtAmount = response.data.debt_amount;
                    self.paidAmount = response.data.paid_amount;
                    self.totalAmount = response.data.total_amount;
                })
                .catch(error => {
                    self.loading = false;
                });
        },
        search() {
            this.fetch();
        },
        add() {
            this.$router.push({ name: "CreateDebt" });
        },
        download() {
            let self = this;
            self.loading = true;
            const { page, itemsPerPage, sortBy, sortDesc } = self.options;
            let data = {
                page: page,
                per_page: itemsPerPage,
                sort_by: sortBy,
                sort_desc: sortDesc,
                search: self.searchText
            };
            this.$store
                .dispatch("debt/export", data)
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
            console.log(id);
            switch (type) {
                case "RouterPush":
                    this.$router.push({
                        name: "DebtDetails",
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
                            .dispatch("debt/delete", { id: id })
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
