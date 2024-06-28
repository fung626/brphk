<template>
    <div>
        <form>
            <v-text-field
                v-model="oldPassword"
                :label="$t('oldpassword')"
                type="password"
                required
                outlined
                dense
            ></v-text-field>
            <v-text-field
                v-model="newPassword"
                :label="$t('newpassword')"
                type="password"
                required
                outlined
                dense
            ></v-text-field>
            <v-text-field
                v-model="confirmPassword"
                :label="$t('confirmpassword')"
                type="password"
                required
                outlined
                dense
            ></v-text-field>
            <CButton @click="update" color="primary" class="px-4">
                {{ $t("button.update") }}
                <v-progress-circular
                    v-if="loading"
                    indeterminate
                    color="primary"
                    :size="15"
                ></v-progress-circular>
            </CButton>
        </form>
    </div>
</template>
<script>
//
export default {
    name: "PasswordForm",
    data() {
        return {
            oldPassword: "",
            newPassword: "",
            confirmPassword: "",
            loading: false
        };
    },
    watch: {},
    methods: {
        update() {
            let self = this;
            self.loading = true;
            let data = {
                old_password: self.oldPassword,
                new_password: self.newPassword,
                confirm_password: self.confirmPassword
            };
            this.$store
                .dispatch("auth/resetpassword", data)
                .then(response => {
                    if (!response.error) {
                        self.oldPassword = "";
                        self.newPassword = "";
                        self.confirmPassword = "";
                        self.$store.dispatch("snackbar/show", {
                            text: self.$t("http.success.resetpwd")
                        });
                    } else {
                        self.$store.dispatch("snackbar/show", {
                            text: self.$t("http.fail.resetpwd")
                        });
                    }
                    self.loading = false;
                })
                .catch(error => {
                    self.$store.dispatch("snackbar/show", {
                        text: self.$t("http.fail.resetpwd")
                    });
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
