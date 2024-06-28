<template>
    <CRow>
        <CCol md="4" xs="12">
            <CRow>
                <CCol>
                    <v-date-picker v-model="dates" range></v-date-picker>
                </CCol>
            </CRow>
            <CRow>
                <CCol>
                    <CButton color="primary" @click="fetch">
                        {{ $t("button.update") }}
                    </CButton>
                </CCol>
            </CRow>
        </CCol>
        <CCol md="8" xs="12">
            <v-data-table
                class="elevation-1"
                :headers="headers"
                :items="items"
                :options.sync="options"
                :server-items-length="serverItemsLength"
                :loading="loading"
                :sort-by.sync="sortBy"
                :sort-desc.sync="sortDesc"
                :hide-default-footer="true"
                :footer-props="{
                    disableItemsPerPage: disableItemsPerPage,
                    disablePagination: disablePagination
                }"
            >
                <template v-slot:[`item.date`]="{ item }">
                    {{ item.date | moment("dddd, Do MMMM YYYY") }}
                </template>
                <template slot="body.append">
                    <tr v-if="!isMobile">
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>{{ $t("total") }} :</th>
                        <th>{{ sum }}</th>
                        <th></th>
                    </tr>
                </template>
            </v-data-table>
        </CCol>
    </CRow>
</template>
<script>
//
import { isMobile } from "mobile-device-detect";

export default {
    name: "BalanceSummary",
    props: {
        id: null
    },
    mounted() {
        this.fetch();
    },
    data() {
        return {
            serverItemsLength: 0,
            items: [],
            loading: false,
            options: {},
            sortBy: "date",
            sortDesc: false,
            disableItemsPerPage: true,
            disablePagination: true,
            headers: [
                { text: this.$t("date"), value: "date" },
                { text: this.$t("company"), value: "company.name_tc" },
                { text: this.$t("bank"), value: "bank_account.bank" },
                {
                    text: `${this.$t("bank")}${this.$t("type")}`,
                    value: "bank_account.account_type"
                },
                { text: this.$t("balance"), value: "balance" },
                { text: this.$t("remark"), value: "remark" }
            ],
            sum: 0,
            dates: [],
            sortedDates: [],
            isMobile: isMobile
        };
    },
    methods: {
        fetch() {
            let self = this;
            if (self.loading) {
                return;
            }
            self.loading = true;
            let sortedDates = [];
            if (self.dates.length > 1) {
                sortedDates = self.dates.sort(
                    (a, b) => new Date(a) - new Date(b)
                );
            }
            const { sortBy, sortDesc } = self.options;
            let data = {
                sort_by: sortBy,
                sort_desc: sortDesc,
                user_id: self.$props.id,
                from_date: sortedDates.length > 1 ? sortedDates[0] : null,
                to_date: sortedDates.length > 1 ? sortedDates[1] : null
            };
            this.$store
                .dispatch("company/bankaccount/balance/summary/get", data)
                .then(response => {
                    self.items = response.data.data;
                    self.serverItemsLength = response.data.total;
                    self.sum = response.data.sum;
                    self.loading = false;
                })
                .catch(error => {
                    self.loading = false;
                });
        }
    }
};
</script>
