<template>
    <div class="py-2">
        <CRow class="p-2">
            <CCol>
                <h4>{{ $t("summary") }}</h4>
            </CCol>
            <CCol class="text-right">
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
                    :hide-default-header="true"
                    :hide-default-footer="true"
                    disable-pagination
                >
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
                            item-text="name"
                            item-value="id"
                            :label="$t('company')"
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
                        <v-select
                            v-model="years"
                            :items="yearOptions"
                            outlined
                            chips
                            :label="$t('years')"
                            deletable-chips
                            multiple
                        ></v-select>
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
// import { mapState } from "vuex";
// import { isMobile } from "mobile-device-detect";
import { years } from "../../../constants";

export default {
    name: "ProfitSummaryTable",
    computed: {
        yearOptions() {
            return years();
        }
    },
    data() {
        return {
            loading: false,
            options: {},
            disableItemsPerPage: true,
            disablePagination: true,
            headers: [],
            serverItemsLength: 0,
            items: [],
            years: [],
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
                // this.fetch();
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
                    .dispatch("profit/company/get", {})
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
    methods: {
        fetch() {
            let self = this;
            if (
                !self.autocomplete.selected ||
                self.autocomplete.selected.length === 0 ||
                self.years.length < 2
            ) {
                self.$store.dispatch("snackbar/show", {
                    text: self.$t("hit.params")
                });
                return;
            }

            if (self.loading) {
                return;
            }
            let sortedYears = [];
            if (self.years.length > 1) {
                sortedYears = self.years.sort(
                    (a, b) => a.split("-")[0] - b.split("-")[0]
                );
            }
            self.loading = true;
            let data = {
                years: sortedYears,
                companies: self.autocomplete.selected
            };
            this.$store
                .dispatch("profit/summary/get", data)
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
        download() {
            let self = this;
            if (
                !self.autocomplete.selected ||
                self.autocomplete.selected.length === 0 ||
                self.years.length < 2
            ) {
                self.$store.dispatch("snackbar/show", {
                    text: self.$t("hit.params")
                });
                return;
            }

            if (self.loading) {
                return;
            }
            let sortedYears = [];
            if (self.years.length > 1) {
                sortedYears = self.years.sort(
                    (a, b) => a.split("-")[0] - b.split("-")[0]
                );
            }
            self.loading = true;
            let data = {
                years: sortedYears,
                companies: self.autocomplete.selected
            };
            this.$store
                .dispatch("profit/summary/export", data)
                .then(response => {
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
