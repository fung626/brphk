<template>
    <div>
        <CRow class="p-2">
            <CCol md="10" sm="10">
                <CInput
                    size="sm"
                    v-model="searchText"
                    v-on:keyup.enter="search"
                >
                    <template #prepend>
                        <CButton color="primary" size="sm" v-on:click="search">
                            <CIcon name="cil-magnifying-glass" size="sm" />
                        </CButton>
                    </template>
                </CInput>
            </CCol>
            <CCol md="2" sm="2" class="text-right">
                <CButton color="primary" size="sm" v-on:click="add">
                    <CIcon name="cil-plus" size="sm" />
                </CButton>
                <CButton color="primary" size="sm" v-on:click="download">
                    <CIcon name="cil-cloud-download" size="sm" />
                </CButton>
                <CButton color="primary" size="sm" v-on:click="reload">
                    <CIcon name="cil-reload" size="sm" />
                </CButton>
            </CCol>
        </CRow>
        <CRow class="p-4">
            <template v-for="(col, i) in filters">
                <div style="float: right; margin-top: 8px" v-bind:key="i">
                    <v-menu :close-on-content-click="false">
                        <template v-slot:activator="{ on, attrs }">
                            <CButton
                                class="m-2"
                                color="primary"
                                size="sm"
                                :disabled="loading"
                                v-bind="attrs"
                                v-on="on"
                            >
                                <CIcon name="cil-filter" size="sm" />
                                {{ col.name }}
                            </CButton>
                        </template>
                        <v-list flat dense class="pa-0">
                            <v-list-item-group multiple class="py-2">
                                <template v-for="(item, i) in col.data">
                                    <v-list-item
                                        :key="`item-${i}`"
                                        :value="item.key"
                                        :ripple="false"
                                    >
                                        <template>
                                            <v-list-item-action>
                                                <v-checkbox
                                                    v-model="item.checkbox"
                                                    :disabled="loading"
                                                    @click="
                                                        onCheckboxClicked(col)
                                                    "
                                                    color="primary"
                                                    dense
                                                ></v-checkbox>
                                            </v-list-item-action>
                                            <v-list-item-content>
                                                <v-list-item-title
                                                    v-text="item.name"
                                                ></v-list-item-title>
                                            </v-list-item-content>
                                        </template>
                                    </v-list-item>
                                </template>
                            </v-list-item-group>
                            <v-divider></v-divider>
                            <CButton class="m-2" color="primary" size="sm">
                                Toggle all
                            </CButton>
                        </v-list>
                    </v-menu>
                </div>
            </template>
        </CRow>
        <v-data-table
            class="elevation-1"
            :expanded.sync="expanded"
            :page="page"
            :pageCount="pageCount"
            :headers="headers"
            :items="items"
            :options.sync="options"
            :server-items-length="serverItemsLength"
            :loading="loading"
            :sort-by.sync="sortBy"
            :sort-desc.sync="sortDesc"
            :footer-props="{
                disableItemsPerPage: disableItemsPerPage,
                disablePagination: disablePagination,
                showFirstLastPage: true,
                showCurrentPage: true,
                itemsPerPageOptions: [10, 20, 50, 100]
            }"
        >
            <template v-slot:[`item.created_at`]="{ item }">
                {{ item.created_at | moment("dddd, Do MMMM YYYY") }}
            </template>
            <template v-slot:[`item.updated_at`]="{ item }">
                {{ item.updated_at | moment("dddd, Do MMMM YYYY") }}
            </template>
            <template v-slot:[`item.actions`]="{ item }">
                <CButtonGroup>
                    <CButton
                        v-for="action in item.actions"
                        :key="action.key"
                        :color="action.color"
                        :disabled="action.disabled"
                        size="sm"
                        @click="click(item.id, action.type)"
                    >
                        {{ action.title }}
                    </CButton>
                </CButtonGroup>
            </template>
        </v-data-table>
    </div>
</template>
<script>
//
export default {
    name: "Earnings",
    components: {},
    props: {
        id: null,
        data: null
    },
    data() {
        return {
            filters: {},
            activeFilters: {},
            searchText: null,
            alert: { show: false, color: "", message: "" },
            expanded: [],
            page: 1,
            serverItemsLength: 0,
            pageCount: 0,
            items: [],
            loading: false,
            options: {},
            sortBy: "created_at",
            sortDesc: false,
            disableItemsPerPage: false,
            disablePagination: false,
            headers: [
                { text: "#ID", value: "id" },
                { text: "Email", value: "email" },
                { text: "Phone", value: "phone" },
                { text: "Role", value: "role" },
                { text: "Created at", value: "created_at" },
                { text: "Updated at", value: "updated_at" },
                { text: "Actions", value: "actions" }
            ]
        };
    },
    watch: {
        options: {
            handler() {
                this.fetch();
            }
        },
        loading() {
            this.disableItemsPerPage = this.loading;
            this.disablePagination = this.loading;
        }
    },
    methods: {
        fetch() {
            let self = this;
            self.loading = true;
            const { page, itemsPerPage, sortBy, sortDesc } = self.options;
            let data = {
                page: page,
                per_page: itemsPerPage,
                sort_by: sortBy,
                sort_desc: sortDesc,
                search: self.searchText
            };
            this.$store
                .dispatch("user/get", data)
                .then(response => {
                    self.items = response.data.data;
                    self.serverItemsLength = response.data.total;
                    self.pageCount = response.data.last_page;
                    self.loading = false;
                })
                .catch(error => {
                    self.loading = false;
                });
        },
        search() {
            this.fetch();
        },
        add() {
            this.$router.push({ name: "CreateEmployee" });
        },
        download() {},
        reload() {
            this.fetch();
        },
        click(id, type) {
            switch (type) {
                case "RouterPush":
                    break;
            }
            // console.log(id, key);
        }
    }
};
</script>
