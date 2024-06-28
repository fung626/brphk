<template>
    <CRow>
        <CCol>
            <CCard class="p-4">
                <CCardBody>
                    <CTabs
                        variant="pills"
                        @update:activeTab="index => activeTabUpdated(index)"
                    >
                        <CTab
                            :title="tab.values[0].toUpperCase()"
                            :active="tab.index === 0 ? true : false"
                        >
                            <hr />
                            <ProfitCompanyForm :id="$route.params.id" />
                        </CTab>
                        <CTab
                            :title="tab.values[1].toUpperCase()"
                            :active="tab.index === 1 ? true : false"
                        >
                            <hr />
                            <ProfitTable :companyId="$route.params.id" />
                        </CTab>
                    </CTabs>
                </CCardBody>
            </CCard>
        </CCol>
    </CRow>
</template>
<script>
//
import ProfitCompanyForm from "./componets/ProfitCompanyForm";
import ProfitTable from "./componets/ProfitTable";

export default {
    name: "ProfitCompanyDetails",
    components: {
        ProfitCompanyForm,
        ProfitTable
    },
    data() {
        return {
            tab: {
                values: [this.$t("info"), this.$t("profit")],
                index: 0
            }
        };
    },
    methods: {
        activeTabUpdated(index) {}
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
