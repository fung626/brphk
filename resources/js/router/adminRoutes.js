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

// Tax
const Tax = () => import("../pages/tax/Tax");
const CreateTax = () => import("../pages/tax/CreateTax");
const TaxDetails = () => import("../pages/tax/TaxDetails");

// Venue
const Venue = () => import("../pages/venue/Venue");
const CreateVenue = () => import("../pages/venue/CreateVenue");
const VenueDetails = () => import("../pages/venue/VenueDetails");

// Venue Item
const CreateVenueItem = () => import("../pages/venueItem/CreateVenueItem");
const CreateVenueItemAmount = () =>
    import("../pages/venueItem/CreateVenueItemAmount");

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
                    path: "/user",
                    meta: { label: i18n.t("user") },
                    name: "User",
                    component: {
                        render(c) {
                            return c("router-view");
                        }
                    },
                    children: [
                        {
                            path: "",
                            meta: { label: i18n.t("table") },
                            name: "User",
                            component: User
                        },
                        {
                            path: "create",
                            meta: { label: i18n.t("create") },
                            name: "CreateUser",
                            component: CreateUser
                        },
                        {
                            path: "details/:id",
                            meta: { label: i18n.t("details") },
                            name: "UserDetails",
                            component: UserDetails
                        }
                    ]
                },
                {
                    path: "debt",
                    meta: { label: i18n.t("debt") },
                    component: {
                        render(c) {
                            return c("router-view");
                        }
                    },
                    children: [
                        {
                            path: "",
                            meta: { label: i18n.t("table") },
                            name: "Debt",
                            component: Debt
                        },
                        {
                            path: "create",
                            meta: { label: i18n.t("create") },
                            name: "CreateDebt",
                            component: CreateDebt
                        },
                        {
                            path: "details/:id",
                            meta: { label: i18n.t("details") },
                            name: "DebtDetails",
                            component: DebtDetails
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
                            path: "bank/create/:id?",
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
                    path: "cashflow",
                    meta: { label: i18n.t("cashflow") },
                    component: {
                        render(c) {
                            return c("router-view");
                        }
                    },
                    children: [
                        {
                            path: "",
                            meta: { label: i18n.t("table") },
                            name: "CashFlow",
                            component: CompanyBalanceCashFlow
                        }
                    ]
                },
                {
                    path: "tax",
                    meta: { label: i18n.t("tax") },
                    component: {
                        render(c) {
                            return c("router-view");
                        }
                    },
                    children: [
                        {
                            path: "",
                            meta: { label: i18n.t("table") },
                            name: "Tax",
                            component: Tax
                        },
                        {
                            path: "create",
                            meta: { label: i18n.t("create") },
                            name: "CreateTax",
                            component: CreateTax
                        },
                        {
                            path: "details/:id",
                            meta: { label: i18n.t("details") },
                            name: "TaxDetails",
                            component: TaxDetails
                        }
                    ]
                },
                {
                    path: "profit",
                    meta: { label: i18n.t("profit") },
                    component: {
                        render(c) {
                            return c("router-view");
                        }
                    },
                    children: [
                        {
                            path: "",
                            meta: { label: i18n.t("table") },
                            name: "Profit",
                            component: Profit
                        },
                        {
                            path: "create/:id",
                            meta: { label: i18n.t("create") },
                            name: "CreateProfit",
                            component: CreateProfit
                        },
                        {
                            path: "company/details/:id",
                            meta: { label: i18n.t("details") },
                            name: "ProfitCompanyDetails",
                            component: ProfitCompanyDetails
                        },
                        {
                            path: "company/create",
                            meta: { label: i18n.t("create") },
                            name: "CreateProfitCompany",
                            component: CreateProfitCompany
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
                },
                {
                    path: "venue",
                    meta: { label: i18n.t("venue") },
                    component: {
                        render(c) {
                            return c("router-view");
                        }
                    },
                    children: [
                        {
                            path: "",
                            meta: { label: i18n.t("table") },
                            name: "Venue",
                            component: Venue
                        },
                        {
                            path: "create",
                            meta: { label: i18n.t("create") },
                            name: "CreateVenue",
                            component: CreateVenue
                        },
                        {
                            path: "details/:id",
                            meta: { label: i18n.t("details") },
                            name: "VenueDetails",
                            component: VenueDetails
                        },
                        {
                            path: "create/item/:id?",
                            meta: {
                                label: `${i18n.t("create")}${i18n.t("item")}`
                            },
                            name: "CreateVenueItem",
                            component: CreateVenueItem
                        },
                        {
                            path: "create/item/amount/:venueId?/:venueItemId?",
                            meta: {
                                label: `${i18n.t("create")}${i18n.t(
                                    "item"
                                )}${i18n.t("amount")}`
                            },
                            name: "CreateVenueItemAmount",
                            component: CreateVenueItemAmount
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
