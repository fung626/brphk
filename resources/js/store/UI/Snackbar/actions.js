import * as types from "./mutation-types";

const name = "snackbar";

export default {
    [`${name}/show`]({ commit, dispatch }, payload) {
        commit(types.SHOW_SNACKBAR, payload);
    },
    [`${name}/close`]({ commit, dispatch }) {
        commit(types.CLOSE_SNACKBAR);
    }
};
