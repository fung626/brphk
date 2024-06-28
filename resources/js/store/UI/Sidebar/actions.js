import * as types from "./mutation-types";

export default {
    toggleSidebarDesktop({ commit, dispatch }, payload) {
        commit(types.TOGGLE_SIDEBAR_DESKTOP, payload);
    },
    toggleSidebarMobile({ commit, dispatch }, payload) {
        commit(types.TOGGLE_SIDEBAR_MOBILE, payload);
    },
    sidebarMinimize({ commit, dispatch }, payload) {
        commit(types.SIDEBAR_MINIMIZE, payload);
    }
};
