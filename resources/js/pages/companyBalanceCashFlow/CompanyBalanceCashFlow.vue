<template>
    <div>
        <CashFlowLineChart />
        <DailyCashFlowTable />
    </div>
</template>
<script>
//
import CashFlowLineChart from "./components/CashFlowLineChart";
import DailyCashFlowTable from "./components/DailyCashFlowTable";
import MonthlyCashFlowTable from "./components/MonthlyCashFlowTable";

export default {
    name: "CompanyBalanceCashFlow",
    props: {},
    components: {
        CashFlowLineChart,
        DailyCashFlowTable,
        MonthlyCashFlowTable
    },
    computed: {},
    data() {
        return {};
    },
    methods: {}
};
</script>
