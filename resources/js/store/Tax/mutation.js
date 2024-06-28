import * as types from "./mutation-types";

export default {
    [types.FETCH_TAX_SUCCESS](state, { data }) {
        state.data = data;
    }
};
