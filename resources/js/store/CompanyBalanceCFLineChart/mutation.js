import * as types from "./mutation-types";

export default {
    [types.FETCH_BALANCE_CF_LINECHART_SUCCESS](state, { data }) {
        state.data = data;
    }
};
