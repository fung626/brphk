<template>
    <CHeader fixed with-subheader light>
        <CToggler
            in-header
            class="ml-3 d-lg-none"
            @click="$store.dispatch('toggleSidebarMobile')"
        />
        <CToggler
            in-header
            class="ml-3 d-md-down-none"
            @click="$store.dispatch('toggleSidebarDesktop')"
        />
        <CHeaderBrand class="mx-auto d-lg-none" to="/">
            <!-- <CIcon name="logo" height="48" alt="Logo" /> -->
            <img src="/images/header-logo.png" height="48" />
        </CHeaderBrand>

        <CMenu />

        <CHeaderNav class="mr-4">
            <TheHeaderDropdownAccnt />
        </CHeaderNav>
        <CSubheader class="px-3">
            <CBreadcrumbRouter class="border-0 mb-0" />
        </CSubheader>
    </CHeader>
</template>

<script>
import CMenu from "./Menu";
import TheHeaderDropdownAccnt from "./TheHeaderDropdownAccnt";

export default {
    name: "TheHeader",
    components: {
        TheHeaderDropdownAccnt,
        CMenu
    }
};
</script>
