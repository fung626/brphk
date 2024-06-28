// import axios from "axios";
import moment from "moment";
import axios from "../../utils/myAxios";
import * as types from "./mutation-types";

const endpoint = "/api/rent/arrears/";
const name = "rent/arrears";

export default {
    [`${name}/get`]({ commit, dispatch }, payload) {
        return new Promise((resolve, reject) => {
            axios
                .post(`${endpoint}get`, payload)
                .then(function(response) {
                    let res = response.data;
                    commit(types.FETCH_RENTS_ARREARS_SUCCESS, res);
                    resolve(res);
                })
                .catch(function(error) {
                    // commit(types.FETCH_COMPANIES_FAILURE);
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
                        `rent-arrears-${datetime}.pdf`
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
