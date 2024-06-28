export default {
    authUser(state) {
        if (state.user === null) {
            return false;
        }
        return state.user;
    },
    authToken(state) {
        if (state.token === null || state.token === undefined) {
            return false;
        }
        return state.token;
    },
    authCheck(state) {
        return state.token !== null && state.token !== undefined;
    },
    isAdmin(state) {
        return state.user?.role === "admin";
    },
    isEmployee(state) {
        return state.user?.role === "employee";
    }
};
