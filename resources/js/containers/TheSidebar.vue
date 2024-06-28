<template>
    <CSidebar
        fixed
        :minimize="minimize"
        :show="show"
        @update:show="value => $store.dispatch('toggleSidebarMobile')"
    >
        <CSidebarBrand class="d-md-down-none" to="/">
            <!-- <CIcon
                class="d-block"
                name="logo"
                size="custom-size"
                :height="35"
                :viewBox="`0 0 ${minimize ? 110 : 556} 134`"
            /> -->
            <img src="/images/logo.png" width="24" />
        </CSidebarBrand>
        <CRenderFunction flat :content-to-render="nav" />
        <CSidebarMinimizer
            class="d-md-down-none"
            @click.native="$store.dispatch('sidebarMinimize')"
        />
    </CSidebar>
</template>

<script>
import axios from "axios";
import { mapState } from "vuex";
import _employeeNav from "./_employeeNav";
import _adminNav from "./_adminNav";

export default {
    name: "TheSidebar",
    data() {
        return {
            nav: [],
            buffor: []
        };
    },
    computed: {
        ...mapState(["uisidebar"]),
        show() {
            return this.uisidebar.sidebarShow;
        },
        minimize() {
            return this.uisidebar.sidebarMinimize;
        }
    },
    methods: {
        dropdown(data) {
            let result = {
                _name: "CSidebarNavDropdown",
                name: $t(data["name"]),
                route: data["href"],
                icon: data["icon"],
                _children: []
            };
            for (let i = 0; i < data["elements"].length; i++) {
                if (data["elements"][i]["slug"] == "dropdown") {
                    result._children.push(this.dropdown(data["elements"][i]));
                } else {
                    result._children.push({
                        _name: "CSidebarNavItem",
                        name: $t(data["elements"][i]["name"]),
                        to: data["elements"][i]["href"],
                        icon: data["elements"][i]["icon"]
                    });
                }
            }
            return result;
        },
        rebuildData(data) {
            this.buffor = [
                {
                    _name: "CSidebarNav",
                    _children: []
                }
            ];
            for (let k = 0; k < data.length; k++) {
                switch (data[k]["slug"]) {
                    case "link":
                        if (data[k]["href"].indexOf("http") !== -1) {
                            this.buffor[0]._children.push({
                                _name: "CSidebarNavItem",
                                name: data[k]["name"],
                                href: data[k]["href"],
                                icon: data[k]["icon"],
                                target: "_blank"
                            });
                        } else {
                            this.buffor[0]._children.push({
                                _name: "CSidebarNavItem",
                                name: data[k]["name"],
                                to: data[k]["href"],
                                icon: data[k]["icon"]
                            });
                        }
                        break;
                    case "title":
                        this.buffor[0]._children.push({
                            _name: "CSidebarNavTitle",
                            _children: [data[k]["name"]]
                        });
                        break;
                    case "dropdown":
                        this.buffor[0]._children.push(this.dropdown(data[k]));
                        break;
                }
            }
            return this.buffor;
        }
    },
    mounted() {
        if (this.$store.getters.isAdmin) {
            this.nav = _adminNav;
        } else {
            this.nav = _employeeNav;
        }
    }
};
</script>
