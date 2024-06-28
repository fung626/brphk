import * as types from "./mutation-types";

export default {
    [types.FETCH_VENUEITEMS_SUCCESS](state, { data }) {
        state.data = data;
    }
};
