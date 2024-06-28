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
                    required
                    outlined
                    dense
                    clearable
                ></v-text-field>
                <v-row>
                    <c-col md="6" sm="6">
                        <v-text-field
                            v-model="formData.phone"
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
                    required
                    outlined
                    dense
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
export default {
    name: "UserForm",
    props: {
        id: null
    },
    data() {
        return {
            formData: {},
            fetchLoading: false,
            updateLoading: false,
            roles: ["admin", "employee"]
        };
    },
    mounted() {
        this.fetch();
    },
    methods: {
        fetch() {
            let self = this;
            self.fetchLoading = true;
            let data = {
                id: self.$props.id
            };
            this.$store
                .dispatch("user/details", data)
                .then(response => {
                    self.formData = response.data.data;
                    self.fetchLoading = false;
                })
                .catch(error => {
                    self.fetchLoading = false;
                    self.$router.push({ name: "Dashboard" });
                    self.$store.dispatch("snackbar/show", {
                        text: self.$t("error.nodata")
                    });
                });
        },
        update() {
            let self = this;
            if (self.updateLoading) {
                return;
            }
            self.updateLoading = true;
            let data = {
                ...self.formData,
                id: self.$props.id
            };
            this.$store
                .dispatch("user/update", data)
                .then(response => {
                    self.formData = response.data.data;
                    self.updateLoading = false;
                })
                .catch(error => {
                    self.updateLoading = false;
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
