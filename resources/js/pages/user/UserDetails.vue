<template>
    <CRow>
        <CCol>
            <CCard class="p-2">
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
                            <UserForm :id="this.$route.params.id"></UserForm>
                        </CTab>
                        <CTab
                            :title="tab.values[1].toUpperCase()"
                            :active="tab.index === 1 ? true : false"
                        >
                            <hr />
                            <BalanceSummary
                                :id="this.$route.params.id"
                            ></BalanceSummary>
                        </CTab>
                    </CTabs>
                </CCardBody>
            </CCard>
        </CCol>
    </CRow>
</template>
<script>
//
import UserForm from "./components/UserForm";
import BalanceSummary from "./components/BalanceSummary";

export default {
    name: "UserDetails",
    components: {
        UserForm,
        BalanceSummary
    },
    data() {
        return {
            tab: {
                values: [
                    this.$t("info"),
                    `${this.$t("balance")}${this.$t("summary")}`
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
