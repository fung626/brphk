<template>
    <CCard class="p-4">
        <CCardBody>
            <h4>{{ $t("create") }}{{ $t("user") }}</h4>
            <hr />
            <form>
                <v-text-field
                    v-model="name"
                    :label="$t('name')"
                    :error="errors.name ? true : false"
                    :error-messages="errors.name"
                    required
                    outlined
                    dense
                    clearable
                ></v-text-field>
                <v-row>
                    <c-col md="6" sm="6">
                        <v-text-field
                            v-model="phone"
                            :label="$t('phone')"
                            :error="errors.phone ? true : false"
                            :error-messages="errors.phone"
                            outlined
                            dense
                            clearable
                        ></v-text-field>
                    </c-col>
                    <c-col md="6" sm="6">
                        <v-text-field
                            v-model="email"
                            :label="$t('email')"
                            :error="errors.email ? true : false"
                            :error-messages="errors.email"
                            required
                            outlined
                            dense
                            clearable
                        ></v-text-field>
                    </c-col>
                </v-row>
                <v-text-field
                    v-model="password"
                    :label="$t('password')"
                    :append-icon="passwordVisible ? 'mdi-eye' : 'mdi-eye-off'"
                    :type="passwordVisible ? 'text' : 'password'"
                    @click:append="passwordVisible = !passwordVisible"
                    required
                    outlined
                    dense
                    clearable
                ></v-text-field>
                <v-select
                    v-model="role"
                    :items="roles"
                    :label="$t('role')"
                    :error="errors.role ? true : false"
                    :error-messages="errors.role"
                    item-text="name"
                    item-value="value"
                    required
                    outlined
                    dense
                ></v-select>
                <CButton @click="submit" color="primary" class="px-4">
                    {{ $t("button.submit") }}
                    <v-progress-circular
                        v-if="loading"
                        indeterminate
                        color="primary"
                        :size="15"
                    ></v-progress-circular>
                </CButton>
            </form>
        </CCardBody>
    </CCard>
</template>

<script>
import { roles } from "../../constants";

export default {
    name: "CreateUser",
    components: {},
    data() {
        return {
            name: "",
            phone: "",
            email: "",
            password: "",
            role: "",
            errors: {},
            loading: false,
            passwordVisible: false,
            roles: roles
        };
    },
    methods: {
        submit() {
            let self = this;
            if (self.loading) {
                return;
            }
            self.loading = true;
            let data = {
                name: self.name,
                email: self.email,
                phone: self.phone,
                password: self.password,
                role: self.role
            };
            this.$store
                .dispatch("user/create", data)
                .then(response => {
                    self.loading = false;
                    self.errors = {};
                    self.$router.back();
                })
                .catch(error => {
                    self.errors = error.response.data?.data;
                    self.loading = false;
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
