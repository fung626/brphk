import * as types from "./mutation-types";

export default {
    [types.SHOW_SNACKBAR](state, { color, text }) {
        state.color = color;
        state.text = text;
        state.show = true;
    },
    [types.CLOSE_SNACKBAR](state) {
        state.show = false;
    }
};
