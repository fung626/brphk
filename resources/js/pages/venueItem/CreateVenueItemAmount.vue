<template>
    <CCard class="p-4">
        <CCardBody>
            <h4>{{ $t("create") }}{{ $t("item") }}{{ $t("amount") }}</h4>
            <hr />
            <form>
                <v-autocomplete
                    v-if="!$route.params.venueItemId"
                    v-model="venueItem"
                    :items="venueItems.items"
                    :loading="venueItems.loading"
                    :search-input.sync="venueItems.search"
                    required
                    outlined
                    dense
                    hide-no-data
                    hide-selected
                    item-text="name"
                    item-value="id"
                    :label="$t('item')"
                    :error="errors['venue_item.id'] ? true : false"
                    :error-messages="errors['venue_item.id']"
                    return-object
                ></v-autocomplete>
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
                <v-select
                    v-model="type"
                    :items="venueAmountTypes"
                    item-text="name"
                    item-value="value"
                    :label="$t('type')"
                    required
                    outlined
                    dense
                    :error="errors.type ? true : false"
                    :error-messages="errors.type"
                ></v-select>
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
                            :error="errors.date ? true : false"
                            :error-messages="errors.date"
                        ></v-text-field>
                    </template>
                    <v-date-picker
                        v-model="date"
                        @input="dateMenu = false"
                        type="month"
                    ></v-date-picker>
                </v-menu>
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
import { venueAmountTypes } from "../../constants";

export default {
    name: "CreateVenueItemAmount",
    components: {},
    data() {
        return {
            venueItem: "",
            name: "",
            amount: "",
            type: "",
            date: "",
            venueItems: {
                items: [],
                loading: false
            },
            loading: false,
            errors: {},
            dateMenu: false,
            venueAmountTypes: venueAmountTypes
        };
    },
    watch: {
        venueItems: [
            function search(val) {
                let self = this;
                if (self.venueItems.length > 0 || self.venueItems.loading)
                    return;
                self.venueItems.loading = true;
                let data = {
                    venue_id: self.$route.params.venueId
                };
                this.$store
                    .dispatch("venue/item/get", data)
                    .then(response => {
                        self.venueItems.items = response.data;
                        self.venueItems.loading = false;
                    })
                    .catch(error => {
                        self.venueItems.loading = false;
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
                venue_item_id: self.$route.params.venueItemId,
                venue_item: self.venueItem,
                type: self.type,
                amount: self.amount,
                date: `${self.date}-01`
            };
            this.$store
                .dispatch("venue/item/amount/create", data)
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
