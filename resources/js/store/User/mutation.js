import * as types from "./mutation-types";

export default {
    [types.FETCH_USERS_SUCCESS](state, { data }) {
        state.data = data;
    }
};
