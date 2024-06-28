<template>
    <CCard class="border-0">
        <v-progress-linear
            :active="fetchLoading"
            indeterminate
            color="cyan"
        ></v-progress-linear>
        <CCardBody>
            <form>
                <v-text-field
                    v-model="formData.name"
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
                            v-model="formData.phone"
                            :error="errors.phone ? true : false"
                            :error-messages="errors.phone"
                            :label="$t('phone')"
                            outlined
                            dense
                            clearable
                        ></v-text-field>
                    </c-col>
                    <c-col md="6" sm="6">
                        <v-text-field
                            v-model="formData.email"
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
                <v-select
                    v-model="formData.role"
                    :items="roles"
                    :label="$t('role')"
                    :error="errors.role ? true : false"
                    :error-messages="errors.role"
                    item-text="name"
                    item-value="value"
                    required
                    outlined
                    dense
                    disabled
                ></v-select>
                <v-text-field
                    v-model="formData.updated_at"
                    :label="$t('updatedat')"
                    outlined
                    dense
                    disabled
                ></v-text-field>
                <v-text-field
                    v-model="formData.created_at"
                    :label="$t('createdat')"
                    outlined
                    dense
                    disabled
                ></v-text-field>
                <CButton @click="update" color="primary" class="px-4">
                    {{ $t("button.update") }}
                    <v-progress-circular
                        v-if="updateLoading"
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
//
import { roles } from "../../../constants";

export default {
    name: "ProfileInfoForm",
    data() {
        return {
            formData: {},
            errors: {},
            fetchLoading: false,
            updateLoading: false,
            roles: roles
        };
    },
    mounted() {
        this.fetch();
    },
    methods: {
        fetch() {
            let self = this;
            self.fetchLoading = true;
            this.$store
                .dispatch("profile/get")
                .then(response => {
                    self.formData = JSON.parse(JSON.stringify(response.data));
                    self.fetchLoading = false;
                    // let data = response.data;
                    // self.formData = data;
                })
                .catch(error => {
                    self.fetchLoading = false;
                });
        },
        update() {
            let self = this;
            if (self.updateLoading) {
                return;
            }
            self.updateLoading = true;
            this.$store
                .dispatch("profile/update", this.formData)
                .then(response => {
                    self.updateLoading = false;
                    self.errors = {};
                })
                .catch(error => {
                    self.updateLoading = false;
                    self.errors = error.response.data;
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
