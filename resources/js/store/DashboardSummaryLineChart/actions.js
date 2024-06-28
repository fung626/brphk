// import axios from "axios";
import axios from "../../utils/myAxios";
import * as types from "./mutation-types";

const endpoint = "/api/dashboard/summary/linechart/";
const name = "dashboard/summary/linechart";

export default {
    [`${name}/get`]({ commit, dispatch }, payload) {
        return new Promise((resolve, reject) => {
            axios
                .post(`${endpoint}get`, payload)
                .then(function(response) {
                    let res = response.data;
                    commit(
                        types.FETCH_DASHBOARD_SUMMARY_LINECHART_SUCCESS,
                        res
                    );
                    resolve(res);
                })
                .catch(function(error) {
                    // commit(types.FETCH_COMPANIES_FAILURE);
                    dispatch("snackbar/show", {
                        color: "error",
                        text: error.response.data.msg
                    });
                    reject(error);
                });
        });
    }
};
