import * as types from "./mutation-types";

export default {
    [types.FETCH_VENUES_SUCCESS](state, { data }) {
        state.data = data;
    }
};
