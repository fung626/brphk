import { i18n } from "../plugins";

export default [
    {
        _name: "CSidebarNav",
        _children: [
            {
                _name: "CSidebarNavItem",
                name: i18n.t("dashboard"),
                to: "/dashboard",
                icon: "cil-speedometer"
            },
            {
                _name: "CSidebarNavTitle",
                _children: [i18n.t("management")]
            },
            {
                _name: "CSidebarNavItem",
                name: i18n.t("user"),
                to: "/user",
                icon: "cil-contact"
            },
            {
                _name: "CSidebarNavItem",
                name: i18n.t("debt"),
                to: "/debt",
                icon: "cil-featured-playlist"
            },
            {
                _name: "CSidebarNavItem",
                name: i18n.t("rent.title"),
                to: "/rent",
                icon: "cil-industry"
            },
            {
                _name: "CSidebarNavItem",
                name: i18n.t("company"),
                to: "/company",
                icon: "cil-spreadsheet"
            },
            {
                _name: "CSidebarNavItem",
                name: i18n.t("cashflow"),
                to: "/cashflow",
                icon: "cil-money"
            },
            {
                _name: "CSidebarNavItem",
                name: i18n.t("tax"),
                to: "/tax",
                icon: "cil-notes"
            },
            {
                _name: "CSidebarNavItem",
                name: i18n.t("profit"),
                to: "/profit",
                icon: "cil-notes"
            },
            {
                _name: "CSidebarNavItem",
                name: i18n.t("expenses"),
                to: "/expenses",
                icon: "cil-notes"
            },
            {
                _name: "CSidebarNavItem",
                name: i18n.t("venue"),
                to: "/venue",
                icon: "cil-building"
            }
        ]
    }
];
