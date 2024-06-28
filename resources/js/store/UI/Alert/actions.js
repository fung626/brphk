import * as types from "./mutation-types";

export default {
    appendAlert({ commit, dispatch }, payload) {
        commit(types.APPEND_ALERT, payload);
    },
    removeAlert({ commit, dispatch }, payload) {
        commit(types.REMOVE_ALERT, payload);
    }
};
