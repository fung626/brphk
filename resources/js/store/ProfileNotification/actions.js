// import axios from "axios";
import axios from "../../utils/myAxios";
import * as types from "./mutation-types";

const endpoint = "/api/profile/notification/";
const name = "profile/notification";

export default {
    [`${name}/get`]({ commit, dispatch }, payload) {
        return new Promise((resolve, reject) => {
            axios
                .get(`${endpoint}get`, payload)
                .then(function(response) {
                    let res = response.data;
                    commit(types.FETCH_PROFILE_NOTIFICATION_SUCCESS, res);
                    resolve(res);
                })
                .catch(function(error) {
                    // commit(types.FETCH_COMPANIES_FAILURE);
                    reject(error);
                });
        });
    },
    [`${name}/update`]({ commit, dispatch }, payload) {
        return new Promise((resolve, reject) => {
            axios
                .post(`${endpoint}update`, payload)
                .then(function(response) {
                    let res = response.data;
                    commit(types.UPDATE_PROFILE_NOTIFICATION_SUCCESS, res.data);
                    resolve(res);
                })
                .catch(function(error) {
                    // commit(types.UPDATE_COMPANY_FAILURE);
                    reject(error);
                });
        });
    }
};
