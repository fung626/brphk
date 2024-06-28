// import axios from "axios";
import moment from "moment";
import queryString from "query-string";
import axios from "../../utils/myAxios";
import * as types from "./mutation-types";

const endpoint = "/api/company/bankaccount/balance/";
const name = "company/bankaccount/balance";

export default {
    [`${name}/get`]({ commit, dispatch }, payload) {
        return new Promise((resolve, reject) => {
            axios
                .post(`${endpoint}get`, payload)
                .then(function(response) {
                    let res = response.data;
                    commit(types.FETCH_COMPANY_BANK_BALANCE_SUCCESS, res);
                    resolve(res);
                })
                .catch(function(error) {
                    // commit(types.FETCH_COMPANIES_FAILURE);
                    reject(error);
                });
        });
    },
    [`${name}/details`]({ commit, dispatch }, payload) {
        let query = queryString.stringify(payload);
        return new Promise((resolve, reject) => {
            axios
                .get(`${endpoint}details?${query}`)
                .then(function(response) {
                    let data = response.data.data;
                    // commit(types.FETCH_COMPANY_DETAILS_SUCCESS, data);
                    resolve(response);
                })
                .catch(function(error) {
                    // commit(types.FETCH_COMPANY_DETAILS_FAILURE);
                    reject(error);
                });
        });
    },
    [`${name}/create`]({ commit, dispatch }, payload) {
        return new Promise((resolve, reject) => {
            axios
                .post(`${endpoint}create`, payload)
                .then(function(response) {
                    let data = response.data.data;
                    // commit(types.CREATE_COMPANY_SUCCESS, data);
                    resolve(response);
                })
                .catch(function(error) {
                    // commit(types.CREATE_COMPANY_FAILURE);
                    reject(error);
                });
        });
    },
    [`${name}/update`]({ commit, dispatch }, payload) {
        return new Promise((resolve, reject) => {
            axios
                .post(`${endpoint}update`, payload)
                .then(function(response) {
                    let data = response.data.data;
                    // commit(types.UPDATE_COMPANY_SUCCESS, data);
                    resolve(response);
                })
                .catch(function(error) {
                    // commit(types.UPDATE_COMPANY_FAILURE);
                    reject(error);
                });
        });
    },
    [`${name}/delete`]({ commit, dispatch }, payload) {
        return new Promise((resolve, reject) => {
            axios
                .post(`${endpoint}delete`, payload)
                .then(function(response) {
                    let data = response.data.data;
                    // commit(types.DELETE_COMPANY_SUCCESS, data);
                    resolve(response);
                })
                .catch(function(error) {
                    // commit(types.DELETE_COMPANY_FAILURE);
                    reject(error);
                });
        });
    },
    [`${name}/export`]({ commit, dispatch }, payload) {
        return new Promise((resolve, reject) => {
            axios({
                url: `${endpoint}export`,
                method: "POST",
                data: payload,
                responseType: "blob"
            })
                .then(response => {
                    let fileURL = window.URL.createObjectURL(
                        new Blob([response.data])
                    );
                    let datetime = moment().format("MMMM Do YYYY, h:mm:ss a");
                    let fileLink = document.createElement("a");
                    fileLink.href = fileURL;
                    fileLink.setAttribute(
                        "download",
                        `bank-account-balance-${datetime}.pdf`
                    );
                    document.body.appendChild(fileLink);
                    fileLink.click();
                    resolve();
                })
                .catch(function(error) {
                    reject(error);
                });
        });
    }
};
