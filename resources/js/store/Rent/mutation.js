import * as types from "./mutation-types";

export default {
    [types.FETCH_RENTS_SUCCESS](state, { data }) {
        if ("data" in data && "total" in data && "last_page" in data) {
            state.data = data;
        }
    }
};
