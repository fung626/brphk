// import Dashboard from "../pages/Dashboard";
// import Welcome from "../components/user/User";
import { i18n } from "../plugins";

const Error404 = () => import("../pages/error/404");
const Error500 = () => import("../pages/error/500");

// auth
const Login = () => import("../pages/auth/Login");
const ForgotPassword = () => import("../pages/auth/ForgotPassword");
const ResetPassword = () => import("../pages/auth/ResetPassword");

const Profile = () => import("../pages/profile/Profile");

const TheContainer = () => import("../containers/TheContainer");

const Dashboard = () => import("../pages/dashboard/Dashboard");

// User
const User = () => import("../pages/user/User");
const CreateUser = () => import("../pages/user/CreateUser");
const UserDetails = () => import("../pages/user/UserDetails");

// Debt
const Debt = () => import("../pages/debt/Debt");
const CreateDebt = () => import("../pages/debt/CreateDebt");
const DebtDetails = () => import("../pages/debt/DebtDetails");

// Rent
const Rent = () => import("../pages/rent/Rent");
const CreateRent = () => import("../pages/rent/CreateRent");
const RentDetails = () => import("../pages/rent/RentDetails");
const CreateRentPayment = () =>
    import("../pages/rent/componets/CreateRentPayment");
const RentPaymentSummary = () =>
    import("../pages/rent/componets/RentPaymentSummary");

// Company
const Company = () => import("../pages/company/Company");
const CreateCompany = () => import("../pages/company/CreateCompany");
const CompanyDetails = () => import("../pages/company/CompanyDetails");

// Company Bank
const CreateCompanyBank = () =>
    import("../pages/companyBank/CreateCompanyBank");
const CompanyBankDetails = () =>
    import("../pages/companyBank/CompanyBankDetails");

// Company Bank Balance
const CreateCompanyBankBalance = () =>
    import("../pages/companyBankBalance/CreateCompanyBankBalance");

// Company Bank CashFlow
const CompanyBalanceCashFlow = () =>
    import("../pages/companyBalanceCashFlow/CompanyBalanceCashFlow");

// Profit
const Profit = () => import("../pages/profit/Profit");
const CreateProfit = () => import("../pages/profit/CreateProfit");
const ProfitCompanyDetails = () =>
    import("../pages/profit/ProfitCompanyDetails");
const CreateProfitCompany = () => import("../pages/profit/CreateProfitCompany");

// Expenses
const Expenses = () => import("../pages/expenses/Expenses");
const CreateExpenses = () => import("../pages/expenses/CreateExpenses");
const ExpensesDetails = () => import("../pages/expenses/ExpensesDetails");

export default ({ authGuard, guestGuard }) => [
    // { path: "*", component: require("../pages/errors/404.vue") }
    // Authenticated routes.
    ...authGuard([
        {
            path: "/",
            redirect: "/dashboard",
            name: i18n.t("home"),
            component: TheContainer,
            children: [
                {
                    path: "/dashboard",
                    name: i18n.t("dashboard"),
                    component: Dashboard
                },
                {
                    path: "/profile",
                    meta: { label: i18n.t("profile") },
                    component: {
                        render(c) {
                            return c("router-view");
                        }
                    },
                    children: [
                        {
                            path: "",
                            component: Profile
                        }
                    ]
                },
                {
                    path: "rent",
                    meta: { label: i18n.t("rent.title") },
                    component: {
                        render(c) {
                            return c("router-view");
                        }
                    },
                    children: [
                        {
                            path: "",
                            meta: { label: i18n.t("table") },
                            name: "Rent",
                            component: Rent
                        },
                        {
                            path: "payment/summary",
                            meta: { label: i18n.t("summary") },
                            name: "RentPaymentSummary",
                            component: RentPaymentSummary
                        },
                        {
                            path: "create",
                            meta: { label: i18n.t("create") },
                            name: "CreateRent",
                            component: CreateRent
                        },
                        {
                            path: "details/:id",
                            meta: { label: i18n.t("details") },
                            name: "RentDetails",
                            component: RentDetails
                        },
                        {
                            path: "create/payment/:id",
                            meta: { label: i18n.t("create") },
                            name: "CreateRentPayment",
                            component: CreateRentPayment
                        }
                    ]
                },
                {
                    path: "company",
                    meta: { label: i18n.t("company") },
                    component: {
                        render(c) {
                            return c("router-view");
                        }
                    },
                    children: [
                        {
                            path: "",
                            meta: { label: i18n.t("table") },
                            name: "Company",
                            component: Company
                        },
                        {
                            path: "create",
                            meta: { label: i18n.t("create") },
                            name: "CreateCompany",
                            component: CreateCompany
                        },
                        {
                            path: "details/:id",
                            meta: { label: i18n.t("details") },
                            name: "CompanyDetails",
                            component: CompanyDetails
                        },
                        {
                            path: "bank/create/:id",
                            meta: {
                                label: `${i18n.t("create")}${i18n.t("bank")}`
                            },
                            name: "CreateCompanyBank",
                            component: CreateCompanyBank
                        },
                        {
                            path: "bank/details/:id",
                            meta: {
                                label: `${i18n.t("bank")}${i18n.t("details")}`
                            },
                            name: "CompanyBankDetails",
                            component: CompanyBankDetails
                        },
                        {
                            path: "bank/balance/create/:id",
                            meta: {
                                label: `${i18n.t("create")}${i18n.t(
                                    "bank"
                                )}${i18n.t("balance")}`
                            },
                            name: "CreateCompanyBankBalance",
                            component: CreateCompanyBankBalance
                        }
                    ]
                },
                {
                    path: "expenses",
                    meta: { label: i18n.t("expenses") },
                    component: {
                        render(c) {
                            return c("router-view");
                        }
                    },
                    children: [
                        {
                            path: "",
                            meta: { label: i18n.t("table") },
                            name: "Expenses",
                            component: Expenses
                        },
                        {
                            path: "create",
                            meta: { label: i18n.t("create") },
                            name: "CreateExpenses",
                            component: CreateExpenses
                        },
                        {
                            path: "details/:id",
                            meta: { label: i18n.t("details") },
                            name: "ExpensesDetails",
                            component: ExpensesDetails
                        }
                    ]
                }
            ]
        }
    ]),
    // Guest routes.
    ...guestGuard([
        {
            path: "/login",
            name: "Login",
            component: Login
        },
        {
            path: "/forgotpassword",
            name: "ForgotPassword",
            component: ForgotPassword
        },
        {
            path: "/resetpassword/:id/:token",
            name: "ResetPassword",
            component: ResetPassword
        },
        { path: "*", component: Error404 }
    ])
];
