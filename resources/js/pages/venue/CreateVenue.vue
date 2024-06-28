<template>
    <CCard class="p-4">
        <CCardBody>
            <h4>{{ $t("create") }}{{ $t("venue") }}</h4>
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
                <v-text-field
                    v-model="remark"
                    :label="$t('remark')"
                    :error="errors.remark ? true : false"
                    :error-messages="errors.remark"
                    required
                    outlined
                    dense
                    clearable
                ></v-text-field>
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
export default {
    name: "CreateVenue",
    components: {},
    data() {
        return {
            name: "",
            remark: "",
            loading: false,
            errors: {}
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
                remark: self.remark
            };
            this.$store
                .dispatch("venue/create", data)
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
