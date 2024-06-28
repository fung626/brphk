<template>
    <div v-if="$store.getters.isAdmin" class="py-4">
        <CRow class="p-2">
            <CCol sm="9">
                <h4>{{ $t("summary") }}</h4>
            </CCol>
            <CCol sm="3" class="text-right">
                <CButton color="primary" size="sm" v-on:click="download">
                    <CIcon name="cil-cloud-download" size="sm" />
                </CButton>
                <CButton color="primary" size="sm" v-on:click="reload">
                    <CIcon name="cil-reload" size="sm" />
                </CButton>
            </CCol>
        </CRow>
        <CRow class="p-2">
            <CCol sm="8">
                <v-data-table
                    class="elevation-1"
                    :headers="headers"
                    :items="items"
                    :options.sync="options"
                    :server-items-length="serverItemsLength"
                    :loading="loading"
                    :hide-default-header="false"
                    :hide-default-footer="true"
                    disable-pagination
                >
                    <template v-slot:[`item.rent.start_date`]="{ item }">
                        <div v-if="item.rent.start_date">
                            {{
                                item.rent.start_date
                                    | moment("dddd, Do MMMM YYYY")
                            }}
                        </div>
                    </template>
                    <template
                        v-slot:[`item.rent.fix_term_tenancy_date`]="{
                            item
                        }"
                    >
                        <div v-if="item.rent.fix_term_tenancy_date">
                            {{
                                item.rent.fix_term_tenancy_date
                                    | moment("dddd, Do MMMM YYYY")
                            }}
                        </div>
                    </template>
                    <template
                        v-slot:[`item.rent.break_clause_date`]="{
                            item
                        }"
                    >
                        <div v-if="item.rent.break_clause_date">
                            {{
                                item.rent.break_clause_date
                                    | moment("dddd, Do MMMM YYYY")
                            }}
                        </div>
                    </template>
                </v-data-table>
            </CCol>
            <CCol sm="4">
                <CCard>
                    <CCardBody>
                        <h4>{{ $t("filter") }} {{ $t("option") }}</h4>
                        <v-autocomplete
                            v-model="autocomplete.selected"
                            :items="autocomplete.items"
                            :loading="autocomplete.loading"
                            :search-input.sync="autocomplete.search"
                            hide-no-data
                            hide-selected
                            outlined
                            item-text="property"
                            item-value="id"
                            :label="$t('rent.property.name')"
                            return-object
                            chips
                            small-chips
                            deletable-chips
                            multiple
                        ></v-autocomplete>
                        <CButton
                            @click="selectall"
                            color="primary"
                            class="my-4"
                        >
                            {{ $t("button.selectall") }}
                        </CButton>
                        <v-date-picker
                            v-model="months"
                            type="month"
                            range
                            full-width
                        ></v-date-picker>
                        <CButton @click="fetch" color="primary" class="my-4">
                            {{ $t("button.update") }}
                        </CButton>
                    </CCardBody>
                </CCard>
            </CCol>
        </CRow>
    </div>
</template>

<script>
//

export default {
    name: "RentPaymentSummary",
    data() {
        return {
            loading: false,
            options: {},
            sortBy: "created_at",
            sortDesc: false,
            headers: [],
            serverItemsLength: 0,
            items: [],
            months: [],
            search: "",
            mounted: false,
            autocomplete: {
                selected: null,
                items: [],
                loading: false
                // search: null
            }
        };
    },
    watch: {
        options: {
            handler() {
                this.fetch();
            }
        },
        autocomplete: [
            function search(val) {
                let self = this;
                if (
                    self.autocomplete.items.length > 0 ||
                    self.autocomplete.loading
                ) {
                    return;
                }
                self.autocomplete.loading = true;
                this.$store
                    .dispatch("rent/get", {})
                    .then(response => {
                        self.autocomplete.items = response.data;
                        self.autocomplete.loading = false;
                    })
                    .catch(error => {
                        self.autocomplete.loading = false;
                    });
            }
        ]
    },
    mounted() {
        // this.fetch();
    },
    methods: {
        fetch() {
            let self = this;
            if (!self.mounted) {
                self.mounted = true;
                return;
            }
            if (
                !self.autocomplete.selected ||
                self.autocomplete.selected?.length === 0 ||
                self.months.length < 2
            ) {
                self.$store.dispatch("snackbar/show", {
                    text: self.$t("hit.params")
                });
                return;
            }

            let sortedMonths = [];
            if (self.months.length > 1) {
                sortedMonths = self.months.sort(
                    (a, b) => new Date(a) - new Date(b)
                );
            }
            self.loading = true;
            const { sortBy, sortDesc } = self.options;
            let data = {
                rent_id: self.$route.params.id,
                rents: self.autocomplete.selected,
                from_month: sortedMonths.length > 1 ? sortedMonths[0] : null,
                to_month: sortedMonths.length > 1 ? sortedMonths[1] : null,
                sort_by: sortBy,
                sort_desc: sortDesc
            };
            this.$store
                .dispatch("rent/payment/summary/get", data)
                .then(response => {
                    let res = response.data;
                    self.headers = res.headers;
                    self.items = res.data;
                    self.serverItemsLength = res.total;
                    self.loading = false;
                })
                .catch(error => {
                    self.loading = false;
                });
        },
        selectall() {
            this.autocomplete.selected = this.autocomplete.items;
        },
        add() {
            this.$router.push({ name: "CreateRentPayment" });
        },
        download() {
            let self = this;
            if (
                self.autocomplete.selected?.length === 0 ||
                self.months.length === 0
            ) {
                return;
            }

            let sortedMonths = [];
            if (self.months.length > 1) {
                sortedMonths = self.months.sort(
                    (a, b) => new Date(a) - new Date(b)
                );
            }
            self.loading = true;
            let data = {
                rent_id: self.$route.params.id,
                rents: self.autocomplete.selected,
                from_month: sortedMonths.length > 1 ? sortedMonths[0] : null,
                to_month: sortedMonths.length > 1 ? sortedMonths[1] : null
            };
            this.$store
                .dispatch("rent/payment/summary/export", data)
                .then(() => {
                    self.loading = false;
                })
                .catch(error => {
                    self.loading = false;
                });
        },
        reload() {
            this.fetch();
        }
    }
};
</script>
