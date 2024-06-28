<template>
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
                    <VenueForm :id="this.$route.params.id"></VenueForm>
                </CTab>
                <CTab
                    :title="tab.values[1].toUpperCase()"
                    :active="tab.index === 1 ? true : false"
                >
                    <hr />
                    <VenueItemTable
                        :venueId="this.$route.params.id"
                    ></VenueItemTable>
                </CTab>
                <CTab
                    :title="tab.values[2].toUpperCase()"
                    :active="tab.index === 2 ? true : false"
                >
                    <hr />
                    <VenueItemAmountTable
                        :venueId="this.$route.params.id"
                    ></VenueItemAmountTable>
                </CTab>
                <CTab
                    :title="tab.values[3].toUpperCase()"
                    :active="tab.index === 3 ? true : false"
                >
                    <hr />
                    <VenueSummaryTable
                        :venueId="this.$route.params.id"
                    ></VenueSummaryTable>
                </CTab>
            </CTabs>
        </CCardBody>
    </CCard>
</template>

<script>
import VenueForm from "./componets/VenueForm";
import VenueItemTable from "../venueItem/VenueItemTable";
import VenueItemAmountTable from "../venueItem/VenueItemAmountTable";
import VenueSummaryTable from "./componets/VenueSummaryTable";


export default {
    name: "VenueDetails",
    components: {
        VenueForm,
        VenueItemTable,
        VenueItemAmountTable,
        VenueSummaryTable
    },
    data() {
        return {
            tab: {
                values: [
                    this.$t("info"),
                    this.$t("item"),
                    `${this.$t("income")}${this.$t("balance")} & ${this.$t(
                        "expenditure"
                    )}${this.$t("balance")}`,
                    this.$t("summary"),
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
