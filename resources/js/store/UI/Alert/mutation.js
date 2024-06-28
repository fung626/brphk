import * as types from "./mutation-types";

export default {
    [types.APPEND_ALERT](state, { id, color, message }) {
        const filteredNullAlerts = state.alerts.filter(function(value) {
            return value != null;
        });
        state.alerts = filteredNullAlerts;
        state.alerts = [...state.alerts, { id, color, message }];
    },
    [types.REMOVE_ALERT](state, { id, color, message }) {
        // console.log(alert);
        const filteredNullAlerts = state.alerts.filter(function(value) {
            return value != null;
        });
        state.alerts = filteredNullAlerts;
        const filteredAlerts = state.alerts.filter(function(value) {
            return value.id !== id;
        });
        state.alerts = filteredAlerts;
    }
};
