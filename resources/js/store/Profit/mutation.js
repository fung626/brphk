import * as types from "./mutation-types";

export default {
    [types.FETCH_PROFIT_SUCCESS](state, { data }) {
        state.data = data;
    }
};
