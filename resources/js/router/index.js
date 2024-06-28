import Vue from "vue";
import Meta from "vue-meta";
import Router from "vue-router";
import { sync } from "vuex-router-sync";
import store from "../store";
import adminRoutes from "./adminRoutes";
import employeeRoutes from "./employeeRoutes";

Vue.use(Meta);
Vue.use(Router);

const router = make(
    store.getters.isAdmin
        ? adminRoutes({ authGuard, guestGuard })
        : employeeRoutes({ authGuard, guestGuard })
);

sync(store, router);

export default router;

/**
 * Create a new router instance.
 *
 * @param  {Array} routes
 * @return {Router}
 */
function make(routes) {
    const router = new Router({
        routes,
        scrollBehavior,
        mode: "history",
        linkActiveClass: "active"
    });

    // Register before guard.
    router.beforeEach(async (to, from, next) => {
        if (!store.getters.authCheck && store.getters.authToken) {
            try {
            } catch (e) {}
        }
        setLayout(router, to);
        next();
    });

    // Register after hook.
    router.afterEach((to, from) => {
        router.app.$nextTick(() => {
            // router.app.$loading.finish();
        });
    });

    return router;
}

/**
 * Set the application layout from the matched page component.
 *
 * @param {Router} router
 * @param {Route} to
 */
function setLayout(router, to) {
    // Get the first matched component.
    const [component] = router.getMatchedComponents({ ...to });

    if (component) {
        router.app.$nextTick(() => {
            // Start the page loading bar.
            if (component.loading !== false) {
                // router.app.$loading.start();
            }

            // Set application layout.
            // router.app.setLayout(component.layout || "");
        });
    }
}

/**
 * Redirect to login if guest.
 *
 * @param  {Array} routes
 * @return {Array}
 */
function authGuard(routes) {
    return beforeEnter(routes, (to, from, next) => {
        if (!store.getters.authCheck) {
            next({
                path: "/login",
                params: { nextUrl: to.fullPath }
            });
        } else {
            next();
        }
    });
}

/**
 * Redirect home if authenticated.
 *
 * @param  {Array} routes
 * @return {Array}
 */
function guestGuard(routes) {
    return beforeEnter(routes, (to, from, next) => {
        if (store.getters.authCheck) {
            next({
                path: "/",
                params: { nextUrl: to.fullPath }
            });
        } else {
            next();
        }
    });
}

/**
 * Apply beforeEnter guard to the routes.
 *
 * @param  {Array} routes
 * @param  {Function} beforeEnter
 * @return {Array}
 */
function beforeEnter(routes, beforeEnter) {
    return routes.map(route => {
        return { ...route, beforeEnter };
    });
}

/**
 * @param  {Route} to
 * @param  {Route} from
 * @param  {Object|undefined} savedPosition
 * @return {Object}
 */
function scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
        return savedPosition;
    }

    const position = {};

    if (to.hash) {
        position.selector = to.hash;
    }

    if (to.matched.some(m => m.meta.scrollToTop)) {
        position.x = 0;
        position.y = 0;
    }

    return position;
}
