<template>
    <CDropdown
        inNav
        class="c-header-nav-items"
        placement="bottom-end"
        add-menu-classes="pt-0"
    >
        <template #toggler>
            <CHeaderNavLink>
                <div class="c-avatar">
                    <img :src="avatar" class="c-avatar-img" />
                </div>
            </CHeaderNavLink>
        </template>
        <CDropdownHeader tag="div" class="text-center" color="light">
            <strong>{{ $t("settings") }}</strong>
        </CDropdownHeader>
        <CDropdownItem to="/profile">
            <CIcon name="cil-user" /> {{ $t("profile") }}
        </CDropdownItem>
        <CDropdownDivider />
        <CDropdownItem @click="logout()">
            <CIcon name="cil-lock-locked" /> {{ $t("logout") }}
        </CDropdownItem>
    </CDropdown>
</template>

<script>
export default {
    name: "TheHeaderDropdownAccnt",
    computed: {
        avatar() {
            return `https://www.gravatar.com/avatar/${this.$store.getters.authUser?.email}?s=160&d=retro`;
        }
    },
    methods: {
        logout() {
            // let self = this;
            this.$store
                .dispatch("auth/logout")
                .then(response => {
                    this.$router.push({ name: "Login" });
                })
                .catch(error => {
                    console.error(error);
                });
        }
    }
};
</script>

<style scoped>
.c-icon {
    margin-right: 0.3rem;
}
</style>
