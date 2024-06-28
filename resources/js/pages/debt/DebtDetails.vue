<template>
    <CCard>
        <v-progress-linear
            :active="fetchLoading"
            indeterminate
            color="cyan"
        ></v-progress-linear>
        <CCardBody>
            <h4>{{ $t("info") }}</h4>
            <hr />
            <CRow v-if="formData.schedule">
                <CCol>
                    <h6>
                        {{ $t("schedule.title") }}{{ $t("type") }}:
                        {{ $t(`schedule.${formData.schedule.type}`) }}
                    </h6>
                </CCol>
            </CRow>
            <CRow v-if="formData.debt_date">
                <CCol>
                    <h6>
                        {{ $t("debtdate") }}:
                        {{ formData.debt_date | moment("dddd, Do MMMM YYYY") }}
                    </h6>
                </CCol>
            </CRow>
            <CRow v-if="formData.due_date">
                <CCol>
                    <h6>
                        {{ $t("duedate") }}:
                        {{ formData.due_date | moment("dddd, Do MMMM YYYY") }}
                    </h6>
                </CCol>
            </CRow>
            <CRow v-if="formData.paid_date">
                <CCol>
                    <h6>
                        {{ $t("paiddate") }}:
                        {{ formData.paid_date | moment("dddd, Do MMMM YYYY") }}
                    </h6>
                </CCol>
            </CRow>
            <hr />
            <form v-on:submit.prevent>
                <v-text-field
                    v-model="formData.item"
                    :label="$t('item')"
                    :error="errors.item ? true : false"
                    :error-messages="errors.item"
                    required
                    outlined
                    dense
                    clearable
                ></v-text-field>
                <v-text-field
                    v-model="formData.amount"
                    :label="$t('amount')"
                    type="number"
                    :error="errors.amount ? true : false"
                    :error-messages="errors.amount"
                    required
                    outlined
                    dense
                    clearable
                ></v-text-field>
                <v-text-field
                    v-model="formData.paid"
                    :label="$t('paid')"
                    type="number"
                    :error="errors.paid ? true : false"
                    :error-messages="errors.paid"
                    outlined
                    dense
                    clearable
                ></v-text-field>
                <v-text-field
                    v-model="formData.remark"
                    :label="$t('remark')"
                    required
                    outlined
                    dense
                    clearable
                >
                </v-text-field>
                <v-text-field
                    v-model="formData.color"
                    class="mb-4"
                    :label="$t('eventcolor')"
                    hide-details
                    outlined
                    dense
                >
                    <template v-slot:append>
                        <v-menu
                            v-model="color.menu"
                            top
                            nudge-bottom="105"
                            nudge-left="16"
                            :close-on-content-click="false"
                        >
                            <template v-slot:activator="{ on }">
                                <div
                                    v-bind:style="{
                                        backgroundColor: formData.color,
                                        cursor: 'pointer',
                                        height: '26px',
                                        width: '26px',
                                        borderRadius: color.menu
                                            ? '50%'
                                            : '4px',
                                        transition:
                                            'border-radius 200ms ease-in-out'
                                    }"
                                    v-on="on"
                                />
                            </template>
                            <v-card>
                                <v-card-text class="pa-0">
                                    <v-color-picker
                                        v-model="formData.color"
                                        hide-canvas
                                        hide-inputs
                                        hide-mode-switch
                                        show-swatches
                                        flat
                                    />
                                </v-card-text>
                            </v-card>
                        </v-menu>
                    </template>
                </v-text-field>

                <CButton @click="update" color="primary" class="px-4 mt-4">
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
export default {
    name: "DebtDetails",
    components: {},
    data() {
        return {
            formData: {},
            color: {
                menu: false
            },
            errors: {},
            updateLoading: false,
            fetchLoading: false
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
                id: self.$route.params.id
            };
            this.$store
                .dispatch("debt/details", data)
                .then(response => {
                    self.formData = response.data.data;
                    self.fetchLoading = false;
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
            let data = {
                ...self.formData,
                id: self.$route.params.id
            };
            self.loading = true;
            this.$store
                .dispatch("debt/update", data)
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
