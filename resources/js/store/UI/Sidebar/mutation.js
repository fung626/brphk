import * as types from "./mutation-types";

export default {
    [types.TOGGLE_SIDEBAR_DESKTOP](state) {
        const sidebarOpened = [true, "responsive"].includes(state.sidebarShow);
        state.sidebarShow = sidebarOpened ? false : "responsive";
    },
    [types.TOGGLE_SIDEBAR_MOBILE](state) {
        const sidebarClosed = [false, "responsive"].includes(state.sidebarShow);
        state.sidebarShow = sidebarClosed ? true : "responsive";
    },
    [types.SIDEBAR_MINIMIZE](state) {
        // const sidebarMinimized = state.sidebarMinimize;
        state.sidebarMinimize = state.sidebarMinimize ? false : true;
    }
};
