// import * as Cookies from "js-cookie";
import SecureLS from "secure-ls";
import Vue from "vue";
import Vuex from "vuex";
import createPersistedState from "vuex-persistedstate";
import Auth from "./Auth";
import Company from "./Company";
import CompanyBalanceCashFlow from "./CompanyBalanceCashFlow";
import CompanyBalanceCFLineChart from "./CompanyBalanceCFLineChart";
import CompanyBalanceSummary from "./CompanyBalanceSummary";
import CompanyBankAccount from "./CompanyBankAccount";
import CompanyBankAccountBalance from "./CompanyBankAccountBalance";
import Dashboard from "./Dashboard";
import DashboardSummaryLineChart from "./DashboardSummaryLineChart";
import Debt from "./Debt";
import ExpectedExpenses from "./ExpectedExpenses";
import Expenses from "./Expenses";
import Profile from "./Profile";
import ProfileNotification from "./ProfileNotification";
import Profit from "./Profit";
import ProfitCompany from "./ProfitCompany";
import ProfitSummary from "./ProfitSummary";
import Rent from "./Rent";
import RentArrears from "./RentArrears";
import RentPayment from "./RentPayment";
import RentPaymentLineChart from "./RentPaymentLineChart";
import RentPaymentSummary from "./RentPaymentSummary";
import Tax from "./Tax";
import UIAlert from "./UI/Alert";
import UISidebar from "./UI/Sidebar";
import UISnackbar from "./UI/Snackbar";
import User from "./User";
import Venue from "./Venue";
import VenueItem from "./VenueItem";
import VenueItemAmount from "./VenueItemAmount";
import VenueSummary from "./VenueSummary";

Vue.use(Vuex);

// Load store modules dynamically.
// const requireContext = require.context("./modules", false, /.*\.js$/);

// const modules = requireContext
//     .keys()
//     .map(file => [file.replace(/(^.\/)|(\.js$)/g, ""), requireContext(file)])
//     .reduce((modules, [name, module]) => {
//         modules[name] = module;
//         return modules;
//     }, {});

const ls = new SecureLS({ isCompression: false });

export default new Vuex.Store({
    strict: process.env.NODE_ENV !== "production",
    modules: {
        // API
        dashboard: Dashboard,
        ["dashboard/summary/linechart"]: DashboardSummaryLineChart,
        auth: Auth,
        profile: Profile,
        ["profile/notification"]: ProfileNotification,
        profit: Profit,
        ["profit/company"]: ProfitCompany,
        ["profit/summary"]: ProfitSummary,
        user: User,
        debt: Debt,
        rent: Rent,
        rentPayment: RentPayment,
        ["rent/arrears"]: RentArrears,
        rentPaymentSummary: RentPaymentSummary,
        rentPaymentLineChart: RentPaymentLineChart,
        tax: Tax,
        company: Company,
        companyBankAccount: CompanyBankAccount,
        companyBankAccountBalance: CompanyBankAccountBalance,
        companyBalanceSummary: CompanyBalanceSummary,
        companyBalanceCashFlow: CompanyBalanceCashFlow,
        companyBalanceCFLineChart: CompanyBalanceCFLineChart,
        ["expected/expenses"]: ExpectedExpenses,
        expenses: Expenses,
        venue: Venue,
        ["venue/item"]: VenueItem,
        ["venue/item/amount"]: VenueItemAmount,
        ["venue/summary"]: VenueSummary,
        // UI
        uisidebar: UISidebar,
        uialert: UIAlert,
        uisnackbar: UISnackbar
    },
    // plugins: [createPersistedState({ storage: window.sessionStorage })]
    plugins: [
        createPersistedState({
            storage: {
                getItem: key => {
                    let item = ls.get(key);
                    if (item) {
                        let state = JSON.parse(item);
                        return state;
                    }
                    return {};
                },
                // Please see https://github.com/js-cookie/js-cookie#json, on how to handle JSON.
                setItem: (key, state) => {
                    let str = JSON.stringify(state);
                    ls.set(key, str);
                },
                removeItem: key => ls.remove(key)
            }
            // storage: {
            //     getItem: key => {
            //         let cookies = Cookies.get(key);
            //         if (cookies) {
            //             let state = JSON.parse(cookies);
            //             return state;
            //         }
            //         return {};
            //     },
            //     // Please see https://github.com/js-cookie/js-cookie#json, on how to handle JSON.
            //     setItem: (key, state) => {
            //         let expireInDays = 7;
            //         let str = JSON.stringify(state);
            //         Cookies.set(key, str, {
            //             expires: expireInDays,
            //             secure: false
            //         });
            //     },
            //     removeItem: key => Cookies.remove(key)
            // }
            // getState: key => {
            //     let cookies = Cookies.get(key);
            //     if (cookies) {
            //         let state = JSON.parse(cookies);
            //         return state;
            //     }
            //     return {};
            // },
            // setState: (key, state) => {
            //     let expireInDays = 7;
            //     let str = JSON.stringify(state);
            //     Cookies.set(key, str, {
            //         expires: expireInDays,
            //         secure: false
            //     });
            // }
        })
    ]
});
