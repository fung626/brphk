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
                name: i18n.t("expenses"),
                to: "/expenses",
                icon: "cil-notes"
            }
        ]
    }
];
