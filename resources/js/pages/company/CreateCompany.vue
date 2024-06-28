<template>
    <CCard class="p-4">
        <CCardBody>
            <form v-on:submit.prevent>
                <h4>{{ $t("create") }}{{ $t("company") }}</h4>
                <hr />
                <v-autocomplete
                    v-model="owner"
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
                ></v-autocomplete>
                <v-row>
                    <c-col>
                        <v-text-field
                            v-model="nameTc"
                            :label="$t('chinese') + $t('name')"
                            required
                            outlined
                            dense
                            clearable
                            maxlength="45"
                        ></v-text-field>
                    </c-col>
                    <c-col>
                        <v-text-field
                            v-model="nameEn"
                            :label="$t('english') + $t('name')"
                            required
                            outlined
                            dense
                            clearable
                            maxlength="45"
                        ></v-text-field>
                    </c-col>
                </v-row>
                <v-row>
                    <c-col md="6" sm="6">
                        <v-text-field
                            v-model="number"
                            label="No."
                            required
                            outlined
                            dense
                            clearable
                        ></v-text-field>
                    </c-col>
                    <c-col md="6" sm="6">
                        <v-text-field
                            v-model="secretary"
                            :label="$t('secretary')"
                            required
                            outlined
                            dense
                            clearable
                            maxlength="45"
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
                                    v-model="incorporationDate"
                                    label="Incorporation Date"
                                    outlined
                                    dense
                                    clearable
                                    readonly
                                    v-bind="attrs"
                                    v-on="on"
                                ></v-text-field>
                            </template>
                            <v-date-picker
                                v-model="incorporationDate"
                                @input="incorporationDateMenu = false"
                            ></v-date-picker>
                        </v-menu>
                    </c-col>
                </v-row>
                <v-text-field
                    v-model="address"
                    :label="$t('address')"
                    required
                    outlined
                    dense
                    clearable
                ></v-text-field>
                <v-text-field
                    v-model="registeredShareCapital"
                    label="Registered Share Capital"
                    required
                    outlined
                    dense
                    clearable
                ></v-text-field>
                <v-text-field
                    v-model="shareHolders"
                    label="Share Holders"
                    :hint="$t('hit.comma')"
                    required
                    outlined
                    dense
                    clearable
                >
                </v-text-field>
                <v-text-field
                    v-model="directors"
                    label="Directors"
                    :hint="$t('hit.comma')"
                    required
                    outlined
                    dense
                    clearable
                >
                </v-text-field>
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
    name: "CreateCompany",
    components: {},
    data() {
        return {
            owner: "",
            nameTc: "",
            nameEn: "",
            number: "",
            secretary: "",
            incorporationDate: "",
            incorporationDateMenu: false,
            address: "",
            registeredShareCapital: "",
            shareHolders: "",
            directors: "",
            loading: false,
            users: {
                items: [],
                loading: false
            }
        };
    },
    watch: {
        users: [
            function search(val) {
                let self = this;
                if (self.users.items.length > 0 || self.users.loading) return;
                self.users.loading = true;
                this.$store
                    .dispatch("user/get", {})
                    .then(response => {
                        self.users.items = response.data;
                        self.users.loading = false;
                    })
                    .catch(error => {
                        self.users.loading = false;
                    });
            }
        ]
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
                name_tc: self.nameTc,
                name_en: self.nameEn,
                number: self.number,
                secretary: self.secretary,
                incorporation_date: self.incorporationDate,
                address: self.address,
                registered_share_capital: self.registeredShareCapital,
                share_holders: self.shareHolders,
                directors: self.directors
            };
            this.$store
                .dispatch("company/create", data)
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
