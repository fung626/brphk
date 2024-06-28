import * as types from "./mutation-types";

export default {
    [types.FETCH_EXPECTED_EXPENSES_SUCCESS](state, { data }) {
        state.data = data;
    }
};
