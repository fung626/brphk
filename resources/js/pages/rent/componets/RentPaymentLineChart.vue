<template>
    <div v-if="$store.getters.isAdmin">
        <CCard>
            <v-progress-linear
                :active="loading"
                indeterminate
                color="cyan"
            ></v-progress-linear>
            <CCardBody>
                <CRow class="p-2">
                    <CCol sm="5">
                        <h4>{{ $t("rent.title") }} {{ $t("chart") }}</h4>
                    </CCol>
                    <CCol sm="7" class="d-none d-md-block">
                        <CButton
                            v-on:click="fetch(period)"
                            color="primary"
                            class="float-right"
                        >
                            <CIcon name="cil-reload" size="sm" />
                        </CButton>
                        <CButtonGroup class="float-right mr-3">
                            <CButton
                                color="outline-secondary"
                                v-for="(value, key) in [
                                    '30 Days',
                                    '3 Months',
                                    '12 Months'
                                ]"
                                :key="key"
                                class="mx-0"
                                :pressed="value === period ? true : false"
                                @click="fetch(value)"
                            >
                                {{ value }}
                            </CButton>
                        </CButtonGroup>
                    </CCol>
                </CRow>
                <CChartLine
                    style="height:300px"
                    :datasets="datasets"
                    :labels="labels"
                    :options="options"
                />
            </CCardBody>
        </CCard>
    </div>
</template>
<script>
//
import { mapState } from "vuex";
import { CChartLine } from "@coreui/vue-chartjs";

export default {
    name: "RentPaymentLineChart",
    components: { CChartLine },
    computed: {
        ...mapState(["rentPaymentLineChart"]),
        labels() {
            return this.rentPaymentLineChart.data?.labels
                ? this.rentPaymentLineChart.data?.labels
                : [];
        },
        datasets() {
            let datasets = this.rentPaymentLineChart.data?.datasets;
            if (datasets) {
                return JSON.parse(JSON.stringify(datasets));
            }
            return [];
        }
    },
    data() {
        return {
            period: "30 Days",
            loading: false,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                tooltips: {
                    mode: "index"
                },
                legend: {
                    display: false
                },
                scales: {
                    xAxes: [
                        {
                            gridLines: {
                                drawOnChartArea: false
                            }
                        }
                    ],
                    yAxes: [
                        {
                            ticks: {
                                beginAtZero: true,
                                callback: (value, index, values) => {
                                    return `${Number(
                                        value
                                    ).abbreviateAmount()}`;
                                }
                            }
                        }
                    ]
                },
                pan: {
                    enabled: true,
                    mode: "x"
                },
                zoom: {
                    enabled: true,
                    mode: "x"
                },
                elements: {
                    point: {
                        radius: 0,
                        hitRadius: 10,
                        hoverRadius: 4,
                        hoverBorderWidth: 3
                    }
                }
            }
        };
    },
    mounted() {
        this.fetch(this.period);
    },
    methods: {
        fetch(period) {
            let self = this;
            if (self.loading) {
                return;
            }
            self.loading = true;
            self.period = period;
            let data = {
                period: period
            };
            this.$store
                .dispatch("rent/payment/linechart/get", data)
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
