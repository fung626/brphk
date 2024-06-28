import * as types from "./mutation-types";

export default {
    [types.FETCH_DASHBOARD_SUCCESS](state, { data }) {
        state.data = data;
    }
};
