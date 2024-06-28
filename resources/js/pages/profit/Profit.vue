<template>
    <div>
        <ProfitCompanyTable />
        <ProfitSummaryTable />
    </div>
</template>
<script>
//
import ProfitCompanyTable from "./componets/ProfitCompanyTable";
import ProfitSummaryTable from "./componets/ProfitSummaryTable";

export default {
    name: "Profit",
    components: {
        ProfitCompanyTable,
        ProfitSummaryTable
    }
};
</script>
