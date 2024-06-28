<template>
    <CCard class="p-4">
        <CCardBody>
            <h4>{{ $t("create") }}{{ $t("item") }}</h4>
            <hr />
            <form>
                <v-autocomplete
                    v-if="!$route.params.id"
                    v-model="venue"
                    :items="venues.items"
                    :loading="venues.loading"
                    :search-input.sync="venues.search"
                    required
                    outlined
                    dense
                    hide-no-data
                    hide-selected
                    item-text="name"
                    item-value="id"
                    :label="$t('venue')"
                    return-object
                ></v-autocomplete>
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
    name: "CreateVenueItem",
    components: {},
    data() {
        return {
            venue: "",
            name: "",
            venues: {
                items: [],
                loading: false
            },
            loading: false,
            errors: {}
        };
    },
    watch: {
        venues: [
            function search(val) {
                let self = this;
                if (self.venues.length > 0 || self.venues.loading) return;
                self.venues.loading = true;
                this.$store
                    .dispatch("venue/get", {})
                    .then(response => {
                        self.venues.items = response.data;
                        self.venues.loading = false;
                    })
                    .catch(error => {
                        self.venues.loading = false;
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
                venue_id: self.$route.params.id,
                venue: self.venue,
                name: self.name
            };
            this.$store
                .dispatch("venue/item/create", data)
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
