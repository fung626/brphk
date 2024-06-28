import * as types from "./mutation-types";

export default {
    [types.FETCH_DEBTS_SUCCESS](state, { data }) {
        state.data = data;
    }
};
