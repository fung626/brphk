<template>
    <div>
        <CompanyTable />
        <div class="py-4">
            <h4>{{ $t("bank") }} {{ $t("table") }}</h4>
            <CompanyBankTable />
        </div>
    </div>
</template>
<script>
//
import { mapState } from "vuex";
import { Dialog } from "../../components";
import CompanyTable from "./components/CompanyTable";
import CompanyBankTable from "../companyBank/components/CompanyBankTable";

export default {
    name: "Employee",
    props: {
        id: null
    },
    components: {
        Dialog,
        CompanyTable,
        CompanyBankTable
    },
    computed: {
        ...mapState(["company"]),
        serverItemsLength() {
            return this.company.data?.total;
        },
        pageCount() {
            return this.company.data?.last_page;
        },
        items() {
            return this.company.data?.data;
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
            sortBy: "created_at",
            sortDesc: false,
            disableItemsPerPage: false,
            disablePagination: false,
            headers: [
                { text: this.$t("owner"), value: "owner.name" },
                {
                    text: `${this.$t("company")}${this.$t("name")}`,
                    value: "name"
                },
                { text: "No.", value: "number" },
                {
                    text: "Incorporation Date",
                    value: "incorporation_date"
                },
                { text: this.$t("address"), value: "address" },
                {
                    text: "Registered Share Capital",
                    value: "registered_share_capital"
                },
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
    methods: {
        fetch() {
            let self = this;
            if (self.loading) {
                return;
            }
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
                .dispatch("company/get", data)
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
            this.$router.push({ name: "CreateCompany" });
        },
        download() {
            let self = this;
            if (self.loading) {
                return;
            }
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
                .dispatch("company/export", data)
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
        async click(id, type) {
            switch (type) {
                case "RouterPush":
                    this.$router.push({
                        name: "CompanyDetails",
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
                            .dispatch("company/delete", { id: id })
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
