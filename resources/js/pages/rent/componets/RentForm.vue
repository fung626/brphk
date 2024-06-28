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
                    v-model="formData.owner"
                    :label="$t('rent.property.owner')"
                    :error="errors.owner ? true : false"
                    :error-messages="errors.owner"
                    required
                    outlined
                    dense
                    clearable
                ></v-text-field>
                <v-text-field
                    v-model="formData.tenant"
                    :label="$t('rent.tenant')"
                    :error="errors.tenant ? true : false"
                    :error-messages="errors.tenant"
                    required
                    outlined
                    dense
                    clearable
                ></v-text-field>
                <v-text-field
                    v-model="formData.property"
                    :label="$t('rent.property.name')"
                    :error="errors.property ? true : false"
                    :error-messages="errors.property"
                    required
                    outlined
                    dense
                    clearable
                ></v-text-field>
                <v-text-field
                    v-model="formData.amount"
                    :label="$t('rent.amount')"
                    :error="errors.amount ? true : false"
                    :error-messages="errors.amount"
                    required
                    outlined
                    dense
                    clearable
                ></v-text-field>
                <v-row>
                    <c-col md="4" sm="4">
                        <v-menu
                            v-model="startDateMenu"
                            :close-on-content-click="false"
                            :nudge-right="40"
                            transition="scale-transition"
                            offset-y
                            min-width="auto"
                        >
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                    v-model="formData.start_date"
                                    :label="$t('rent.startdate')"
                                    outlined
                                    dense
                                    clearable
                                    readonly
                                    :error="errors.start_date ? true : false"
                                    :error-messages="errors.start_date"
                                    v-bind="attrs"
                                    v-on="on"
                                ></v-text-field>
                            </template>
                            <v-date-picker
                                v-model="formData.start_date"
                                @input="startDateMenu = false"
                            ></v-date-picker>
                        </v-menu>
                    </c-col>
                    <c-col md="4" sm="4">
                        <v-menu
                            v-model="fixTermTenancyDateMenu"
                            :close-on-content-click="false"
                            :nudge-right="40"
                            transition="scale-transition"
                            offset-y
                            min-width="auto"
                        >
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                    v-model="formData.fix_term_tenancy_date"
                                    :label="$t('rent.fttdate')"
                                    outlined
                                    dense
                                    clearable
                                    readonly
                                    :error="
                                        errors.fix_term_tenancy_date
                                            ? true
                                            : false
                                    "
                                    :error-messages="
                                        errors.fix_term_tenancy_date
                                    "
                                    v-bind="attrs"
                                    v-on="on"
                                ></v-text-field>
                            </template>
                            <v-date-picker
                                v-model="formData.fix_term_tenancy_date"
                                @input="fixTermTenancyDateMenu = false"
                            ></v-date-picker>
                        </v-menu>
                    </c-col>
                    <c-col md="4" sm="4">
                        <v-menu
                            v-model="breakClauseDateMenu"
                            :close-on-content-click="false"
                            :nudge-right="40"
                            transition="scale-transition"
                            offset-y
                            min-width="auto"
                        >
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                    v-model="formData.break_clause_date"
                                    :label="$t('rent.bcdate')"
                                    outlined
                                    dense
                                    clearable
                                    readonly
                                    :error="
                                        errors.break_clause_date ? true : false
                                    "
                                    :error-messages="errors.break_clause_date"
                                    v-bind="attrs"
                                    v-on="on"
                                ></v-text-field>
                            </template>
                            <v-date-picker
                                v-model="formData.break_clause_date"
                                @input="breakClauseDateMenu = false"
                            ></v-date-picker>
                        </v-menu>
                    </c-col>
                </v-row>
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
    name: "RentDetails",
    components: {},
    props: {
        id: null
    },
    data() {
        return {
            formData: {},
            startDateMenu: false,
            breakClauseDateMenu: false,
            fixTermTenancyDateMenu: false,
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
                .dispatch("rent/details", data)
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
                .dispatch("rent/update", self.formData)
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
