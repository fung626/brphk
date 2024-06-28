import * as types from "./mutation-types";

export default {
    [types.FETCH_VENUEITEMAOUNTS_SUCCESS](state, { data }) {
        state.data = data;
    }
};
