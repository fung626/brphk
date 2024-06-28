<template>
    <div>
        <Snackbar />
        <CContainer class="c-app flex-row align-items-center">
            <CRow class="justify-content-center">
                <CCol md="8">
                    <CCard class="p-4">
                        <CCardBody>
                            <CForm @submit.prevent="submit" method="POST">
                                <h1>{{ $t("auth.forgotpassword.title") }}</h1>
                                <p class="text-muted">
                                    {{ $t("auth.forgotpassword.msg") }}
                                </p>
                                <v-text-field
                                    v-model="email"
                                    :label="$t('email')"
                                    type="email"
                                    :error="errors.email ? true : false"
                                    :error-messages="errors.email"
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
                                            {{ $t("button.submit") }}
                                            <v-progress-circular
                                                v-if="fetching"
                                                indeterminate
                                                color="primary"
                                                :size="15"
                                            ></v-progress-circular>
                                        </CButton>
                                    </CCol>
                                </CRow>
                            </CForm>
                        </CCardBody>
                    </CCard>
                </CCol>
            </CRow>
        </CContainer>
    </div>
</template>

<script>
//
import axios from "axios";
import { Snackbar } from "../../components";

export default {
    name: "Forgot",
    components: {
        Snackbar
    },
    data() {
        return {
            email: "",
            errors: {},
            fetching: false
        };
    },
    methods: {
        submit(event) {
            let self = this;
            self.fetching = true;
            axios
                .post("/api/auth/forgotpassword", {
                    email: self.email
                })
                .then(function(response) {
                    self.fetching = false;
                    self.errors = {};
                    let msg =
                        "We have emailed you a link to reset your password";
                    self.email = "";
                    self.$store.dispatch("snackbar/show", {
                        text: msg
                    });
                })
                .catch(function(error) {
                    self.fetching = false;
                    self.errors = error.response.data?.data;
                });
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
