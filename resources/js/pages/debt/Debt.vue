<template>
    <div>
        <DebtCalendar />
        <ReceivableTable />
        <PayableTable />
    </div>
</template>

<script>
import { mapState } from "vuex";
import { isMobile } from "mobile-device-detect";
import DebtCalendar from "./componets/DebtCalendar";
import ReceivableTable from "./componets/ReceivableTable";
import PayableTable from "./componets/PayableTable";

export default {
    name: "Debt",
    components: {
        DebtCalendar,
        ReceivableTable,
        PayableTable
    },
    computed: {
        ...mapState(["debt"]),
        serverItemsLength() {
            return this.debt.data?.total;
        },
        pageCount() {
            return this.debt.data?.last_page;
        },
        items() {
            return this.debt.data?.data;
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
                { text: this.$t("item"), value: "item" },
                { text: this.$t("amount"), value: "amount" },
                { text: this.$t("paid"), value: "paid" },
                { text: this.$t("debtdate"), value: "debt_date" },
                { text: this.$t("duedate"), value: "due_date" },
                { text: this.$t("paiddate"), value: "paid_date" },
                { text: this.$t("createdat"), value: "created_at" },
                { text: this.$t("updatedat"), value: "updated_at" },
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
