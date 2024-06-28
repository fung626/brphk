<template>
    <div class="py-2">
        <h4>{{ $t("table") }}</h4>
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

export default {
    name: "ProfitCompanyTable",
    computed: {
        ...mapState(["profit/company"]),
        serverItemsLength() {
            return this["profit/company"].data?.total;
        },
        pageCount() {
            return this["profit/company"].data?.last_page;
        },
        items() {
            return this["profit/company"].data?.data;
        }
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
                { text: this.$t("name"), value: "name" },
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
                page: page,
                per_page: itemsPerPage,
                sort_by: sortBy,
                sort_desc: sortDesc,
                search: self.searchText
            };
            this.$store
                .dispatch("profit/company/get", data)
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
            this.$router.push({ name: "CreateProfitCompany" });
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
                .dispatch("profit/company/export", data)
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
                        name: "ProfitCompanyDetails",
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
                            .dispatch("profit/comapny/delete", { id: id })
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
