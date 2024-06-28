import actions from "./actions";
import getters from "./getter";
import mutations from "./mutation";
import state from "./state";

export default {
    namespaced: false,
    state: state,
    mutations: mutations,
    getters: getters,
    actions: actions
};
