<template>
    <CCard class="border-0">
        <v-progress-linear
            :active="fetchLoading"
            indeterminate
            color="cyan"
        ></v-progress-linear>
        <CCardBody>
            <form v-on:submit.prevent>
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
                <v-text-field
                    v-model="formData.remark"
                    :label="$t('remark')"
                    :error="errors.remark ? true : false"
                    :error-messages="errors.remark"
                    required
                    outlined
                    dense
                    clearable
                ></v-text-field>
                <hr />
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
    name: "ProfitCompanyForm",
    props: {
        id: null
    },
    data() {
        return {
            formData: "",
            errors: {},
            fetchLoading: false,
            updateLoading: false
        };
    },
    mounted() {
        this.fetch();
    },
    methods: {
        fetch() {
            let self = this;
            if (self.fetchLoading) {
                return;
            }
            self.fetchLoading = true;
            let data = {
                id: self.$props.id
            };
            this.$store
                .dispatch("profit/company/details", data)
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
            this.$store
                .dispatch("profit/company/update", self.formData)
                .then(response => {
                    self.formData = response.data.data;
                    self.updateLoading = false;
                    self.errors = {};
                })
                .catch(error => {
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
