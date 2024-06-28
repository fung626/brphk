<template>
    <CCard class="p-4">
        <CCardBody>
            <form v-on:submit.prevent>
                <h4>{{ $t("create") }}{{ $t("rent.title") }}</h4>
                <hr />
                <v-text-field
                    v-model="owner"
                    :label="$t('rent.property.owner')"
                    :error="errors.owner ? true : false"
                    :error-messages="errors.owner"
                    required
                    outlined
                    dense
                    clearable
                ></v-text-field>
                <v-text-field
                    v-model="tenant"
                    :label="$t('rent.tenant')"
                    :error="errors.tenant ? true : false"
                    :error-messages="errors.tenant"
                    required
                    outlined
                    dense
                    clearable
                ></v-text-field>
                <v-row>
                    <c-col md="6" sm="6">
                        <v-text-field
                            v-model="property"
                            :label="$t('rent.property.name')"
                            :error="errors.property ? true : false"
                            :error-messages="errors.property"
                            required
                            outlined
                            dense
                            clearable
                        ></v-text-field>
                    </c-col>
                    <c-col md="6" sm="6">
                        <v-text-field
                            v-model="area"
                            :label="$t('rent.area')"
                            :error="errors.area ? true : false"
                            :error-messages="errors.area"
                            required
                            outlined
                            dense
                            clearable
                        ></v-text-field>
                    </c-col>
                </v-row>
                <v-row>
                    <c-col md="4" sm="4">
                        <v-text-field
                            v-model="amount"
                            :label="$t('rent.amount')"
                            type="number"
                            :error="errors.amount ? true : false"
                            :error-messages="errors.amount"
                            required
                            outlined
                            dense
                            clearable
                        ></v-text-field>
                    </c-col>
                    <c-col md="4" sm="4">
                        <v-text-field
                            v-model="rentPerSquareFoot"
                            :label="$t('rent.rentpersquarefoot')"
                            type="number"
                            :error="errors.rent_per_square_foot ? true : false"
                            :error-messages="errors.rent_per_square_foot"
                            required
                            outlined
                            dense
                            clearable
                        ></v-text-field>
                    </c-col>
                    <c-col md="4" sm="4">
                        <v-text-field
                            v-model="governmentRent"
                            :label="$t('rent.governmentrent')"
                            type="number"
                            :error="errors.government_rent ? true : false"
                            :error-messages="errors.government_rent"
                            required
                            outlined
                            dense
                            clearable
                        ></v-text-field>
                    </c-col>
                </v-row>
                <v-row>
                    <c-col md="4" sm="4">
                        <v-text-field
                            v-model="rates"
                            :label="$t('rent.rates')"
                            type="number"
                            :error="errors.rates ? true : false"
                            :error-messages="errors.rates"
                            required
                            outlined
                            dense
                            clearable
                        ></v-text-field>
                    </c-col>
                    <c-col md="4" sm="4">
                        <v-text-field
                            v-model="managementFee"
                            :label="$t('rent.managementfee')"
                            type="number"
                            :error="errors.management_fee ? true : false"
                            :error-messages="errors.management_fee"
                            required
                            outlined
                            dense
                            clearable
                        ></v-text-field>
                    </c-col>
                    <c-col md="4" sm="4">
                        <v-text-field
                            v-model="otherFee"
                            :label="$t('rent.otherfee')"
                            type="number"
                            :error="errors.other_fee ? true : false"
                            :error-messages="errors.other_fee"
                            required
                            outlined
                            dense
                            clearable
                        ></v-text-field>
                    </c-col>
                </v-row>
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
                                    v-model="startDate"
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
                                v-model="startDate"
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
                                    v-model="fixTermTenancyDate"
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
                                v-model="fixTermTenancyDate"
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
                                    v-model="breakClauseDate"
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
                                v-model="breakClauseDate"
                                @input="breakClauseDateMenu = false"
                            ></v-date-picker>
                        </v-menu>
                    </c-col>
                </v-row>
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
                <hr />
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
//

export default {
    name: "CreateRent",
    data() {
        return {
            owner: "",
            tenant: "",
            property: "",
            amount: null,
            managementFee: null,
            rates: null,
            rentPerSquareFoot: null,
            governmentRent: null,
            otherFee: null,
            area: null,
            startDate: "",
            startDateMenu: false,
            breakClauseDate: "",
            breakClauseDateMenu: false,
            fixTermTenancyDate: "",
            fixTermTenancyDateMenu: false,
            remark: "",
            errors: {},
            loading: false
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
                owner: self.owner,
                tenant: self.tenant,
                property: self.property,
                amount: self.amount,
                management_fee: self.managementFee,
                rates: self.rates,
                rent_per_square_foot: self.rentPerSquareFoot,
                government_rent: self.governmentRent,
                other_fee: self.otherFee,
                area: self.area,
                start_date: self.startDate,
                fix_term_tenancy_date: self.fixTermTenancyDate,
                break_clause_date: self.breakClauseDate,
                remark: self.remark
            };
            this.$store
                .dispatch("rent/create", data)
                .then(response => {
                    self.loading = false;
                    self.$router.back();
                })
                .catch(error => {
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
