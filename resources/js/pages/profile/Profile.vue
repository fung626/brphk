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
                            <ProfileInfoForm />
                        </CTab>
                        <CTab
                            :title="tab.values[1].toUpperCase()"
                            :active="tab.index === 1 ? true : false"
                        >
                            <hr />
                            <PasswordForm />
                        </CTab>
                        <CTab
                            :title="tab.values[2].toUpperCase()"
                            :active="tab.index === 2 ? true : false"
                        >
                            <hr />
                            <NotificationSettings />
                        </CTab>
                    </CTabs>
                </CCardBody>
            </CCard>
        </CCol>
    </CRow>
</template>
<script>
//
import ProfileInfoForm from "./components/ProfileInfoForm";
import PasswordForm from "./components/PasswordForm";
import NotificationSettings from "./components/NotificationSettings";

export default {
    name: "Profile",
    components: {
        ProfileInfoForm,
        PasswordForm,
        NotificationSettings
    },
    props: {},
    data() {
        return {
            tab: {
                values: [
                    this.$t("info"),
                    this.$t("password"),
                    this.$t("notification")
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
