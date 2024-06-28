<template>
    <div>
        <Snackbar />
        <CContainer class="c-app flex-row align-items-center">
            <CRow class="justify-content-center">
                <CCol md="8">
                    <v-progress-linear
                        :active="fetchLoading"
                        indeterminate
                        color="cyan"
                    ></v-progress-linear>
                    <CCard class="p-4">
                        <CCardBody>
                            <CForm
                                autocomplete="off"
                                @submit.prevent="submit"
                                method="POST"
                            >
                                <h1>{{ $t("auth.resetpassword.title") }}</h1>
                                <p class="text-muted">
                                    {{ $t("auth.resetpassword.msg") }}
                                </p>
                                <v-text-field
                                    v-model="password"
                                    :label="$t('password')"
                                    type="password"
                                    :error="errors.password ? true : false"
                                    :error-messages="errors.password"
                                    required
                                    outlined
                                    dense
                                ></v-text-field>
                                <v-text-field
                                    v-model="confirmPassword"
                                    :label="$t('confirmpassword')"
                                    type="password"
                                    :error="
                                        errors.confirm_password ? true : false
                                    "
                                    :error-messages="errors.confirm_password"
                                    required
                                    outlined
                                    dense
                                ></v-text-field>
                                <button type="submit" class="btn btn-primary">
                                    {{ $t("button.reset") }}
                                    <v-progress-circular
                                        v-if="updateLoading"
                                        indeterminate
                                        color="primary"
                                        :size="15"
                                    ></v-progress-circular>
                                </button>
                            </CForm>
                        </CCardBody>
                    </CCard>
                </CCol>
            </CRow>
        </CContainer>
    </div>
</template>

<script>
import axios from "axios";
import { Snackbar } from "../../components";

export default {
    components: { Snackbar },
    data() {
        return {
            errors: {},
            fetchLoading: false,
            updateLoading: false,
            password: null,
            confirmPassword: null
        };
    },
    mounted() {
        this.fetch();
    },
    methods: {
        fetch() {
            let self = this;
            let data = {
                id: this.$route.params.id,
                token: this.$route.params.token
            };
            self.fetchLoading = true;
            axios
                .post("/api/auth/forgotpassword/find", data)
                .then(function(response) {
                    self.fetchLoading = false;
                })
                .catch(function(error) {
                    self.fetchLoading = false;
                    self.$store.dispatch("snackbar/show", {
                        text: error.response.data.msg
                    });
                    self.$router.push({ name: "Login" });
                });
        },
        submit() {
            let self = this;
            let data = {
                id: this.$route.params.id,
                token: this.$route.params.token,
                password: self.password,
                confirm_password: self.confirmPassword
            };
            self.updateLoading = true;
            axios
                .post("/api/auth/forgotpassword/reset", data)
                .then(function(response) {
                    self.updateLoading = false;
                    self.errors = {};
                    self.$store.dispatch("snackbar/show", {
                        text: "Password Has Been Successfully Reset"
                    });
                    self.$router.push({ name: "Login" });
                })
                .catch(function(error) {
                    self.updateLoading = false;
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
