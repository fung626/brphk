<template>
    <CCard class="p-4">
        <CCardBody>
            <form v-on:submit.prevent>
                <h4>{{ $t("create") }}{{ $t("expectedexpenses") }}</h4>
                <hr />
                <v-text-field
                    v-model="company"
                    :label="$t('company')"
                    :error="errors.company ? true : false"
                    :error-messages="errors.company"
                    required
                    outlined
                    dense
                    clearable
                ></v-text-field>
                <v-text-field
                    v-model="item"
                    :label="$t('item')"
                    :error="errors.item ? true : false"
                    :error-messages="errors.item"
                    required
                    outlined
                    dense
                    clearable
                ></v-text-field>
                <v-text-field
                    v-model="amount"
                    :label="$t('amount')"
                    type="number"
                    :error="errors.amount ? true : false"
                    :error-messages="errors.amount"
                    required
                    outlined
                    dense
                    clearable
                ></v-text-field>
                <v-menu
                    v-model="dateMenu"
                    :close-on-content-click="false"
                    :nudge-right="40"
                    transition="scale-transition"
                    offset-y
                    min-width="auto"
                >
                    <template v-slot:activator="{ on, attrs }">
                        <v-text-field
                            v-model="date"
                            :label="$t('date')"
                            outlined
                            dense
                            clearable
                            readonly
                            v-bind="attrs"
                            v-on="on"
                        ></v-text-field>
                    </template>
                    <v-date-picker
                        v-model="date"
                        @input="dateMenu = false"
                    ></v-date-picker>
                </v-menu>
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
export default {
    name: "CreateExpectedExpenses",
    data() {
        return {
            company: "",
            item: "",
            amount: "",
            date: "",
            dateMenu: false,
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
                company: self.company,
                item: self.item,
                amount: self.amount,
                date: self.date,
                remark: self.remark
            };
            this.$store
                .dispatch("expected/expenses/create", data)
                .then(() => {
                    self.loading = false;
                    self.errors = {};
                    self.$router.back();
                })
                .catch(error => {
                    self.loading = false;
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
