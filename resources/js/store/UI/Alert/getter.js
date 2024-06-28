export default {
    alerts(state) {
        if (state.alerts === null) {
            return false;
        }
        return state.alerts;
    }
};
