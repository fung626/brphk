import * as types from "./mutation-types";

export default {
    [types.FETCH_RENT_PAYMENT_LINECHART_SUCCESS](state, { data }) {
        state.data = data;
    }
};
