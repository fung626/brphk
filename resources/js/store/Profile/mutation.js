import * as types from "./mutation-types";

export default {
    [types.FETCH_PROFILE_SUCCESS](state, { data }) {
        state.data = data;
    },
    [types.UPDATE_PROFILE_SUCCESS](state, { data }) {
        state.data = data;
    }
};
