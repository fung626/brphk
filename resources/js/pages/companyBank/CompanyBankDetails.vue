<template>
    <CRow>
        <Snackbar />
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
                            <CompanyBankForm
                                :id="this.$route.params.id"
                            ></CompanyBankForm>
                        </CTab>
                        <CTab
                            :title="tab.values[1].toUpperCase()"
                            :active="tab.index === 1 ? true : false"
                        >
                            <hr />
                            <CompanyBankBalanceTable
                                :companyBankAccountId="this.$route.params.id"
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
import CompanyBankForm from "./components/CompanyBankForm";
import CompanyBankBalanceTable from "../companyBankBalance/components/CompanyBankBalanceTable";
import Snackbar from "../../components/Snackbar";

export default {
    name: "CompanyBankDetails",
    props: {},
    components: {
        Snackbar,
        CompanyBankForm,
        CompanyBankBalanceTable
    },
    data() {
        return {
            tab: {
                values: [this.$t("info"), this.$t("balance")],
                index: 0
            }
        };
    },
    methods: {
        activeTabUpdated(index) {}
    }
};
</script>
