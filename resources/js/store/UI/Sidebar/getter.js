export default {
    sidebarShow(state) {
        if (state.sidebarShow === null) {
            return false;
        }
        return state.sidebarShow;
    },
    sidebarMinimize(state) {
        if (state.sidebarMinimize === null) {
            return false;
        }
        return state.sidebarMinimize;
    }
};
