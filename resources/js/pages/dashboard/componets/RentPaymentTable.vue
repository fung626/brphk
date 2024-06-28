<template>
    <div v-if="$store.getters.isAdmin">
        <CCol>
            <CRow>
                <strong class="h4">
                    {{ `${$t("rent.title")} ${$t("table")}` }}
                </strong>
            </CRow>
        </CCol>
        <Dialog ref="dialog" />
        <v-data-table
            class="elevation-1"
            :headers="headers"
            :items="items"
            :options.sync="options"
            :server-items-length="serverItemsLength"
            :loading="loading"
            hide-default-footer
        >
            <template v-slot:[`item.payment_date`]="{ item }">
                <div v-if="item.payment_date">
                    {{ item.payment_date | moment("dddd, Do MMMM YYYY") }}
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
import { Dialog } from "../../../components";

export default {
    name: "RentPayment",
    components: {
        Dialog
    },
    computed: {
        ...mapState(["rentPayment"]),
        serverItemsLength() {
            return this.rentPayment.data?.total;
        },
        pageCount() {
            return this.rentPayment.data?.last_page;
        },
        items() {
            return this.rentPayment.data?.data;
        }
    },
    data() {
        return {
            page: 1,
            loading: false,
            options: {},
            sortBy: "created_at",
            sortDesc: true,
            disableItemsPerPage: false,
            disablePagination: false,
            headers: [
                { text: this.$t("user"), value: "users.name", sortable: false },
                {
                    text: this.$t("rent.property.owner"),
                    value: "rent.owner",
                    sortable: false
                },
                {
                    text: this.$t("rent.property.name"),
                    value: "rent.property",
                    sortable: false
                },
                {
                    text: this.$t("rent.tenant"),
                    value: "rent.tenant",
                    sortable: false
                },
                { text: this.$t("amount"), value: "amount", sortable: false },
                {
                    text: `${this.$t("payment")}${this.$t("month")}`,
                    value: "payment_month",
                    sortable: false
                },
                {
                    text: `${this.$t("payment")}${this.$t("date")}`,
                    value: "payment_date",
                    sortable: false
                },
                {
                    text: this.$t("createdat"),
                    value: "created_at",
                    sortable: false
                },
                {
                    text: this.$t("updatedat"),
                    value: "updated_at",
                    sortable: false
                },
                {
                    text: this.$t("actions"),
                    value: "actions",
                    sortable: false,
                    sortable: false
                }
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
        }
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
                .dispatch("rent/payment/get", data)
                .then(response => {
                    self.loading = false;
                })
                .catch(error => {
                    self.loading = false;
                });
        },
        async click(id, type) {
            switch (type) {
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
                            .dispatch("rent/payment/delete", { id: id })
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
