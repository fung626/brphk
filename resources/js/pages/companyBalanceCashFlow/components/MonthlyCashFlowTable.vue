<template>
    <div>
        <CRow class="p-2">
            <CCol>
                <h4>{{ $t("schedule.monthly") }}{{ $t("cashflow") }}</h4>
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
                            :label="$t('user')"
                            return-object
                            chips
                            small-chips
                            deletable-chips
                            multiple
                        ></v-autocomplete>
                        <v-date-picker
                            v-model="dates"
                            range
                            full-width
                            type="month"
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
    name: "MonthlyCashFlowTable",
    data() {
        return {
            loading: false,
            options: {},
            disableItemsPerPage: true,
            disablePagination: true,
            headers: [],
            serverItemsLength: 0,
            items: [],
            users: [],
            dates: [],
            autocomplete: {
                selected: null,
                items: [],
                loading: false
                // search: null
            }
        };
    },
    mounted() {
        // this.fetch();
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
                    .dispatch("user/get", {})
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
                self.autocomplete.selected?.length === 0 ||
                !self.dates ||
                self.dates.length < 2
            ) {
                self.$store.dispatch("snackbar/show", {
                    text: self.$t("hit.params")
                });
                return;
            }
            let sortedDates = [];
            if (self.dates.length > 1) {
                sortedDates = self.dates.sort(
                    (a, b) => new Date(a) - new Date(b)
                );
            }
            if (self.loading) {
                return;
            }
            self.loading = true;
            let data = {
                type: "monthly",
                users: self.autocomplete.selected,
                from_date:
                    sortedDates.length > 1 ? `${sortedDates[0]}-01` : null,
                to_date: sortedDates.length > 1 ? `${sortedDates[1]}-28` : null
            };
            this.$store
                .dispatch("company/bankaccount/balance/cashflow/get", data)
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
        download() {
            let self = this;
            if (
                !self.autocomplete.selected ||
                self.autocomplete.selected?.length === 0 ||
                !self.dates ||
                self.dates.length < 2
            ) {
                self.$store.dispatch("snackbar/show", {
                    text: self.$t("hit.params")
                });
                return;
            }
            let sortedDates = [];
            if (self.dates.length > 1) {
                sortedDates = self.dates.sort(
                    (a, b) => new Date(a) - new Date(b)
                );
            }
            if (self.loading) {
                return;
            }
            self.loading = true;
            let data = {
                type: "monthly",
                users: self.autocomplete.selected,
                from_date:
                    sortedDates.length > 1 ? `${sortedDates[0]}-01` : null,
                to_date: sortedDates.length > 1 ? `${sortedDates[1]}-28` : null
            };
            this.$store
                .dispatch("company/bankaccount/balance/cashflow/export", data)
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

<style scoped>
.v-text-field >>> input {
    font-size: 0.8em;
    font-weight: 100;
}
.v-text-field >>> label {
    font-size: 0.8em;
}
.v-text-field >>> button {
    font-size: 0.8em;
}
</style>
