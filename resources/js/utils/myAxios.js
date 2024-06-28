import axios from "axios";
import store from "../store";

const config = {
    // mode: "no-cors",
    // httpsAgent: new https.Agent({ rejectUnauthorized: false }),
    // headers: {
    //     "Content-Type": "application/json",
    //     Accept: "application/json",
    //     "Access-Control-Allow-Origin": "*"
    // }
};

const instance = axios.create(config);
instance.interceptors.request.use(function(config) {
    const token = store.getters.authToken;
    config.headers.Authorization = token ? `Bearer ${token}` : "";
    return config;
});

export default instance;
