<template>
    <div>
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
                        <RentForm :id="this.$route.params.id"></RentForm>
                    </CTab>
                    <CTab
                        :title="tab.values[1].toUpperCase()"
                        :active="tab.index === 1 ? true : false"
                    >
                        <hr />
                        <RentPaymentTable
                            :id="this.$route.params.id"
                        ></RentPaymentTable>
                    </CTab>
                </CTabs>
            </CCardBody>
        </CCard>
    </div>
</template>

<script>
//
import RentForm from "./componets/RentForm";
import RentPaymentTable from "./componets/RentPaymentTable";

export default {
    name: "RentDetails",
    components: {
        RentForm,
        RentPaymentTable
    },
    data() {
        return {
            tab: {
                values: [this.$t("info"), this.$t("rent.title")],
                index: 0
            }
        };
    },
    methods: {
        activeTabUpdated(index) {}
    }
};
</script>
