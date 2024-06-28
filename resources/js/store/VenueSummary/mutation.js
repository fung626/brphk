import * as types from "./mutation-types";

export default {
    [types.FETCH_VENUESUMMARY_SUCCESS](state, { data }) {
        state.data = data;
    }
};
