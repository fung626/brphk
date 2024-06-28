import * as types from "./mutation-types";

export default {
    [types.FETCH_PROFIT_SUMMARY_SUCCESS](state, { data }) {
        state.data = data;
    }
};
