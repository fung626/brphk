// import axios from "axios";
import moment from "moment";
import queryString from "query-string";
import axios from "../../utils/myAxios";
import * as types from "./mutation-types";

const endpoint = "/api/venue/summary/";
const name = "venue/summary";

export default {
    [`${name}/get`]({ commit, dispatch }, payload) {
        return new Promise((resolve, reject) => {
            axios
                .post(`${endpoint}get`, payload)
                .then(function(response) {
                    let res = response.data;
                    commit(types.FETCH_VENUESUMMARY_SUCCESS, res.data);
                    resolve(res);
                })
                .catch(function(error) {
                    // commit(types.FETCH_EMPLOYEES_FAILURE);
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
                    fileLink.setAttribute("download", `venue-summary-${datetime}.pdf`);
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
