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
                            <CompanyForm
                                :id="this.$route.params.id"
                            ></CompanyForm>
                        </CTab>
                        <CTab
                            :title="tab.values[1].toUpperCase()"
                            :active="tab.index === 1 ? true : false"
                        >
                            <hr />
                            <CompanyBankTable
                                :id="this.$route.params.id"
                            ></CompanyBankTable>
                        </CTab>
                        <CTab
                            :title="tab.values[2].toUpperCase()"
                            :active="tab.index === 2 ? true : false"
                        >
                            <hr />
                            <CompanyBankBalanceTable
                                :companyId="this.$route.params.id"
                            ></CompanyBankBalanceTable>
                        </CTab>
                    </CTabs>
                </CCardBody>
            </CCard>
        </CCol>
    </CRow>
</template>
<script>
//
import CompanyBankBalanceTable from "../companyBankBalance/components/CompanyBankBalanceTable";
import CompanyBankTable from "../companyBank/components/CompanyBankTable";
import CompanyForm from "./components/CompanyForm";

export default {
    name: "CompanyDetails",
    components: {
        CompanyForm,
        CompanyBankTable,
        CompanyBankBalanceTable
    },
    data() {
        return {
            tab: {
                values: [
                    this.$t("info"),
                    this.$t("bank"),
                    `${this.$t("bank")}${this.$t("balance")}`
                ],
                index: 0
            }
        };
    },
    methods: {
        activeTabUpdated(index) {}
    }
};
</script>
