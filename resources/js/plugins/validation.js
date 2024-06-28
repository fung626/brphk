import * as VeeValidate from "vee-validate";
import Vue from "vue";

const permissions = ["dashboard", "users", "tasks"];

Vue.use(VeeValidate, { delay: 250 });

Vue.mixin({
    $_veeValidate: {
        validator: "new"
    },
    methods: {
        async formHasErrors() {
            const valid = await this.$validator.validateAll();
            if (valid) {
                this.$validator.pause();
            }
            return !valid;
        }
    }
});
