import * as types from "./mutation-types";

export default {
    [types.FETCH_BALANCE_CASHFLOW_SUCCESS](state, { data }) {
        state.data = data;
    }
};
