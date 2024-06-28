import * as types from "./mutation-types";

export default {
    [types.FETCH_COMPANY_BANKACCOUNTS_SUCCESS](state, { data }) {
        state.data = data;
    }
};
