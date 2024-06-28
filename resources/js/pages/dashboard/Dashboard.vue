<template>
    <div>
        <!-- <WidgetsDropdown /> -->
        <DashboardSummaryLineChart />
        <CRow>
            <CCol md="12">
                <CCard>
                    <CCardBody>
                        <CRow>
                            <CCol sm="12" lg="6">
                                <CRow>
                                    <CCol sm="6">
                                        <CCallout color="primary">
                                            <small class="text-muted">
                                                {{ $t("total") }}
                                                {{ $t("rent.title") }}
                                                {{ $t("payment") }}
                                            </small>
                                            <br />
                                            <strong class="h4">
                                                {{ rentPayment }}
                                            </strong>
                                        </CCallout>
                                    </CCol>
                                    <CCol sm="6">
                                        <CCallout color="warning">
                                            <small class="text-muted">
                                                {{ $t("total") }}
                                                {{ $t("bank") }}
                                                {{ $t("balance") }}
                                            </small>
                                            <br />
                                            <strong class="h4">
                                                {{ bankBalance }}
                                            </strong>
                                        </CCallout>
                                    </CCol>
                                </CRow>
                            </CCol>
                            <CCol sm="12" lg="6">
                                <CRow>
                                    <CCol sm="6">
                                        <CCallout color="danger">
                                            <small class="text-muted">
                                                {{ $t("total") }}
                                                {{ $t("expenses") }}
                                            </small>
                                            <br />
                                            <strong class="h4">
                                                {{ expenses }}
                                            </strong>
                                        </CCallout>
                                    </CCol>
                                    <CCol sm="6">
                                        <CCallout color="info">
                                            <small class="text-muted">
                                                {{ $t("total") }}
                                                {{ $t("tax") }}
                                            </small>
                                            <br />
                                            <strong class="h4">
                                                {{ tax }}
                                            </strong>
                                        </CCallout>
                                    </CCol>
                                </CRow>
                            </CCol>
                        </CRow>
                        <br />
                        <RentPaymentTable />
                    </CCardBody>
                </CCard>
            </CCol>
        </CRow>
    </div>
</template>

<script>
import { mapState } from "vuex";
import DashboardSummaryLineChart from "./componets/DashboardSummaryLineChart";
import RentPaymentTable from "./componets/RentPaymentTable";

export default {
    name: "Dashboard",
    components: {
        DashboardSummaryLineChart,
        RentPaymentTable
    },
    computed: {
        ...mapState(["dashboard"]),
        rentPayment() {
            return this.dashboard.data?.rent_payment;
        },
        bankBalance() {
            return this.dashboard.data?.bank_balance;
        },
        expenses() {
            return this.dashboard.data?.expenses;
        },
        tax() {
            return this.dashboard.data?.tax;
        }
    },
    data() {
        return {};
    },
    mounted() {
        this.fetch();
    },
    methods: {
        fetch() {
            let data = {};
            this.$store
                .dispatch("dashboard/get", data)
                .then(response => {})
                .catch(error => {});
        }
    }
};
</script>
