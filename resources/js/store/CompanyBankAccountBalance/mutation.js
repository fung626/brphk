import * as types from "./mutation-types";

export default {
    [types.FETCH_COMPANY_BANK_BALANCE_SUCCESS](state, { data }) {
        state.data = data;
    }
};
