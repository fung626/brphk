<template>
    <div>
        <RentPaymentLineChart />
        <RentTable />
        <RentArrearsTable />
        <RentPaymentSummary />
    </div>
</template>
<script>
//
import RentPaymentLineChart from "./componets/RentPaymentLineChart";
import RentTable from "./componets/RentTable";
import RentPaymentSummary from "./componets/RentPaymentSummary";
import RentArrearsTable from "./componets/RentArrearsTable";

export default {
    name: "Rent",
    components: {
        RentPaymentLineChart,
        RentTable,
        RentPaymentSummary,
        RentArrearsTable
    }
};
</script>
