import axios from "../../utils/myAxios";
import * as types from "./mutation-types";

const endpoint = "/api/auth/";
const name = "auth";

export default {
    [`login`]({ commit, dispatch }, payload) {
        return new Promise((resolve, reject) => {
            axios
                .post(`${endpoint}login`, payload)
                .then(function(response) {
                    let data = response.data.data;
                    commit(types.LOGIN_SUCCESS, data);
                    resolve(response);
                })
                .catch(function(error) {
                    reject(error);
                });
        });
    },
    [`${name}/logout`]({ commit }) {
        return new Promise((resolve, reject) => {
            axios
                .post(`${endpoint}logout`, {})
                .then(function(response) {
                    // console.log(response);s
                    let data = response.data;
                    commit(types.LOGOUT);
                    resolve(response);
                })
                .catch(function(error) {
                    reject(error);
                });
        });
    },
    [`${name}/resetpassword`]({ commit, dispatch }, payload) {
        return new Promise((resolve, reject) => {
            axios
                .post(`${endpoint}resetpassword`, payload)
                .then(function(response) {
                    resolve(response);
                })
                .catch(function(error) {
                    reject(error);
                });
        });
    }
};
