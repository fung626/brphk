// import axios from "axios";
import moment from "moment";
import queryString from "query-string";
import axios from "../../utils/myAxios";
import * as types from "./mutation-types";

const endpoint = "/api/venue/item/amount/";
const name = "venue/item/amount";

export default {
    [`${name}/get`]({ commit, dispatch }, payload) {
        return new Promise((resolve, reject) => {
            axios
                .post(`${endpoint}get`, payload)
                .then(function(response) {
                    let res = response.data;
                    commit(types.FETCH_VENUEITEMAOUNTS_SUCCESS, res.data);
                    resolve(res);
                })
                .catch(function(error) {
                    // commit(types.FETCH_EMPLOYEES_FAILURE);
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
                    resolve(response);
                })
                .catch(function(error) {
                    // commit(types.FETCH_EMPLOYEES_FAILURE);
                    reject(error);
                });
        });
    },
    [`${name}/create`]({ commit, dispatch }, payload) {
        return new Promise((resolve, reject) => {
            axios
                .post(`${endpoint}create`, payload)
                .then(function(response) {
                    dispatch("snackbar/show", {
                        color: "success",
                        text: "Successfully Created"
                    });
                    resolve(response);
                })
                .catch(function(error) {
                    dispatch("snackbar/show", {
                        color: "error",
                        text: error.response.data.msg
                    });
                    reject(error);
                });
        });
    },
    [`${name}/update`]({ commit, dispatch }, payload) {
        return new Promise((resolve, reject) => {
            axios
                .post(`${endpoint}update`, payload)
                .then(function(response) {
                    dispatch("snackbar/show", {
                        color: "success",
                        text: "Successfully Updated"
                    });
                    resolve(response);
                })
                .catch(function(error) {
                    dispatch("snackbar/show", {
                        color: "error",
                        text: error.response.data.msg
                    });
                    reject(error);
                });
        });
    },
    [`${name}/delete`]({ commit, dispatch }, payload) {
        return new Promise((resolve, reject) => {
            axios
                .post(`${endpoint}delete`, payload)
                .then(function(response) {
                    dispatch("snackbar/show", {
                        color: "success",
                        text: "Successfully Deleted"
                    });
                    resolve(response);
                })
                .catch(function(error) {
                    dispatch("snackbar/show", {
                        color: "error",
                        text: error.response.data.msg
                    });
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
                        `venue-item-amount-${datetime}.pdf`
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
