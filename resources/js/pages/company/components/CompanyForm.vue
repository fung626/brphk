<template>
    <CCard class="border-0">
        <v-progress-linear
            :active="fetchLoading"
            indeterminate
            color="cyan"
        ></v-progress-linear>
        <CCardBody>
            <form v-on:submit.prevent>
                <v-autocomplete
                    v-if="$store.getters.isAdmin"
                    v-model="formData.owner"
                    :items="users.items"
                    :loading="users.loading"
                    :search-input.sync="users.search"
                    required
                    outlined
                    dense
                    hide-no-data
                    hide-selected
                    item-text="name"
                    item-value="id"
                    :label="$t('owner')"
                    return-object
                    :disabled="!$store.getters.isAdmin"
                ></v-autocomplete>
                <v-row>
                    <c-col>
                        <v-text-field
                            v-model="formData.name_tc"
                            :label="`${$t('chinese')}${$t('name')}`"
                            required
                            outlined
                            dense
                            clearable
                            maxlength="45"
                            :disabled="!$store.getters.isAdmin"
                        ></v-text-field>
                    </c-col>
                    <c-col>
                        <v-text-field
                            v-model="formData.name_en"
                            :label="`${$t('english')}${$t('name')}`"
                            required
                            outlined
                            dense
                            clearable
                            maxlength="45"
                            :disabled="!$store.getters.isAdmin"
                        ></v-text-field>
                    </c-col>
                </v-row>
                <v-row>
                    <c-col md="6" sm="6">
                        <v-text-field
                            v-model="formData.number"
                            label="No."
                            required
                            outlined
                            dense
                            clearable
                            :disabled="!$store.getters.isAdmin"
                        ></v-text-field>
                    </c-col>
                    <c-col md="6" sm="6">
                        <v-text-field
                            v-model="formData.secretary"
                            :label="$t('secretary')"
                            required
                            outlined
                            dense
                            clearable
                            maxlength="45"
                            :disabled="!$store.getters.isAdmin"
                        ></v-text-field>
                    </c-col>
                </v-row>
                <v-row>
                    <c-col md="6" sm="6">
                        <v-menu
                            v-model="incorporationDateMenu"
                            :close-on-content-click="false"
                            :nudge-right="40"
                            transition="scale-transition"
                            offset-y
                            min-width="auto"
                        >
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                    v-model="formData.incorporation_date"
                                    label="Incorporation Date"
                                    outlined
                                    dense
                                    clearable
                                    readonly
                                    v-bind="attrs"
                                    v-on="on"
                                    :disabled="!$store.getters.isAdmin"
                                ></v-text-field>
                            </template>
                            <v-date-picker
                                v-model="formData.incorporation_date"
                                @input="incorporationDateMenu = false"
                            ></v-date-picker>
                        </v-menu>
                    </c-col>
                </v-row>
                <v-text-field
                    v-model="formData.address"
                    :label="$t('address')"
                    required
                    outlined
                    dense
                    clearable
                    :disabled="!$store.getters.isAdmin"
                ></v-text-field>
                <v-text-field
                    v-model="formData.registered_share_capital"
                    label="Registered Share Capital"
                    required
                    outlined
                    dense
                    clearable
                    :disabled="!$store.getters.isAdmin"
                ></v-text-field>
                <v-text-field
                    v-model="formData.share_holders"
                    label="Share Holders"
                    :hint="$t('hit.comma')"
                    required
                    outlined
                    dense
                    clearable
                    :disabled="!$store.getters.isAdmin"
                >
                </v-text-field>
                <v-text-field
                    v-model="formData.directors"
                    label="Directors"
                    :hint="$t('hit.comma')"
                    required
                    outlined
                    dense
                    clearable
                    :disabled="!$store.getters.isAdmin"
                >
                </v-text-field>
                <hr />
                <CButton
                    @click="update"
                    v-if="$store.getters.isAdmin"
                    color="primary"
                    class="px-4"
                >
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
    name: "CompanyForm",
    components: {},
    props: {
        id: null
    },
    data() {
        return {
            formData: {},
            users: {
                items: [],
                loading: false
            },
            incorporationDateMenu: false,
            fetchLoading: false,
            updateLoading: false
        };
    },
    watch: {
        users: [
            function search(val) {
                let self = this;
                if (self.users.length > 0 || self.users.loading) return;
                self.users.loading = true;
                this.$store
                    .dispatch("user/get", {})
                    .then(response => {
                        self.users.items = response.data;
                        self.users.loading = false;
                    })
                    .catch(error => {
                        self.loading = false;
                        self.$router.push({ name: "Dashboard" });
                        self.$store.dispatch("snackbar/show", {
                            text: self.$t("error.nodata")
                        });
                    });
            }
        ]
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
                .dispatch("company/details", data)
                .then(response => {
                    self.fetchLoading = false;
                    self.formData = response.data.data;
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
                .dispatch("company/update", self.formData)
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
