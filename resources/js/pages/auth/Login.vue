<template>
    <div>
        <Snackbar />
        <CContainer class="c-app flex-row align-items-center">
            <CRow class="justify-content-center">
                <CCol md="8">
                    <CCardGroup>
                        <CCard class="p-4">
                            <CCardBody>
                                <CForm @submit.prevent="login" method="POST">
                                    <h1>{{ $t("login") }}</h1>
                                    <p class="text-muted">
                                        {{ $t("auth.signin.msg") }}
                                    </p>
                                    <v-text-field
                                        v-model="email"
                                        :label="$t('email')"
                                        type="email"
                                        required
                                        outlined
                                        dense
                                    ></v-text-field>
                                    <v-text-field
                                        v-model="password"
                                        :label="$t('password')"
                                        type="password"
                                        required
                                        outlined
                                        dense
                                    ></v-text-field>
                                    <CRow>
                                        <CCol col="6" class="text-left">
                                            <CButton
                                                type="submit"
                                                color="primary"
                                                class="px-4"
                                            >
                                                {{ $t("login") }}
                                                <v-progress-circular
                                                    v-if="fetching"
                                                    indeterminate
                                                    color="primary"
                                                    :size="15"
                                                ></v-progress-circular>
                                            </CButton>
                                        </CCol>
                                        <CCol col="6" class="text-right">
                                            <CButton
                                                @click="forgotpassword"
                                                color="link"
                                                class="px-0"
                                            >
                                                {{ $t("forgotpassword") }}
                                            </CButton>
                                        </CCol>
                                    </CRow>
                                </CForm>
                            </CCardBody>
                        </CCard>
                        <CCard
                            text-color="white"
                            class="text-center py-5 d-md-down-none"
                            body-wrapper
                        >
                            <img src="/images/logo-named.png" width="256" />
                        </CCard>
                    </CCardGroup>
                </CCol>
            </CRow>
        </CContainer>
    </div>
</template>

<script>
//
import { mapActions } from "vuex";
import { Snackbar } from "../../components";

export default {
    name: "Login",
    components: { Snackbar },
    data() {
        return {
            email: "",
            password: "",
            fetching: false
        };
    },
    methods: {
        ...mapActions(["login"]),
        login(event) {
            let self = this;
            self.fetching = true;
            self.$store
                .dispatch("login", {
                    email: self.email,
                    password: self.password
                })
                .then(function(response) {
                    self.fetching = false;
                    self.$store.dispatch("snackbar/show", {
                        text: self.$t("http.success.login")
                    });
                    // self.email = "";
                    // self.password = "";
                    // self.$router.push({ name: "" });
                    window.location.reload();
                })
                .catch(error => {
                    self.fetching = false;
                    self.$store.dispatch("snackbar/show", {
                        text: self.$t("http.fail.login")
                    });
                });
        },
        forgotpassword() {
            this.$router.push({ name: "ForgotPassword" });
        }
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
