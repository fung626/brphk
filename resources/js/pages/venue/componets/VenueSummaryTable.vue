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
                        <v-date-picker
                            v-model="months"
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

export default {
    name: "VenueSummary",
    props: {
        venueId: null
    },
    data() {
        return {
            loading: false,
            options: {},
            headers: [],
            serverItemsLength: 0,
            items: [],
            months: [],
        };
    },
    mounted() {
        // this.fetch();
    },
    methods: {
        fetch() {
            let self = this;
            if (
                !self.months ||
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
            if (self.loading) {
                return;
            }
            self.loading = true;
            let data = {
                venue_id: self.$props.venueId,
                from_month: sortedMonths.length > 1 ? sortedMonths[0] : null,
                to_month: sortedMonths.length > 1 ? sortedMonths[1] : null
            };
            this.$store
                .dispatch("venue/summary/get", data)
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
        reload() {
            this.fetch();
        },
        download() {
            let self = this;
            if (
                !self.months ||
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

            if (self.loading) {
                return;
            }
            self.loading = true;
            let data = {
                venue_id: self.$props.venueId,
                from_month: sortedMonths.length > 1 ? sortedMonths[0] : null,
                to_month: sortedMonths.length > 1 ? sortedMonths[1] : null
            };

            this.$store
                .dispatch("venue/summary/export", data)
                .then(() => {
                    self.loading = false;
                })
                .catch(error => {
                    self.loading = false;
                }); 
        }
    }
};
</script>
