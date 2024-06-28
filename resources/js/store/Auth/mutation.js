import * as types from "./mutation-types";

export default {
    [types.LOGIN_SUCCESS](state, payload) {
        sessionStorage.setItem("token", payload.token);
        state.user = payload;
        state.token = payload.token;
    },
    [types.LOGOUT](state) {
        state.user = null;
        state.token = null;
        // sessionStorage.token = null;
        sessionStorage.removeItem("token");
    }
};
