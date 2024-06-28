import { i18n } from "../plugins";

const schedule = {
    type: [
        {
            name: `${i18n.t("schedule.daily")}`,
            value: "daily"
        },
        {
            name: `${i18n.t("schedule.weekly")}`,
            value: "weekly"
        },
        {
            name: `${i18n.t("schedule.monthly")}`,
            value: "monthly"
        },
        {
            name: `${i18n.t("schedule.yearly")}`,
            value: "yearly"
        },
        {
            name: `${i18n.t("schedule.specific")}`,
            value: "specific"
        }
    ],
    months: [
        "01",
        "02",
        "03",
        "04",
        "05",
        "06",
        "07",
        "08",
        "09",
        "10",
        "11",
        "12"
    ],
    weekDays: [
        {
            name: `${i18n.t("schedule.Monday")}`,
            value: "Monday"
        },
        {
            name: `${i18n.t("schedule.Tuesday")}`,
            value: "Tuesday"
        },
        {
            name: `${i18n.t("schedule.Wednesday")}`,
            value: "Wednesday"
        },
        {
            name: `${i18n.t("schedule.Thursday")}`,
            value: "Thursday"
        },
        {
            name: `${i18n.t("schedule.Friday")}`,
            value: "Friday"
        },
        {
            name: `${i18n.t("schedule.Saturday")}`,
            value: "Saturday"
        },
        {
            name: `${i18n.t("schedule.Sunday")}`,
            value: "Sunday"
        }
    ],
    days: [
        "01",
        "02",
        "03",
        "04",
        "05",
        "06",
        "07",
        "08",
        "09",
        "10",
        "11",
        "12",
        "13",
        "14",
        "15",
        "16",
        "17",
        "18",
        "19",
        "20",
        "22",
        "23",
        "24",
        "25",
        "26",
        "27",
        "28",
        "29",
        "30",
        "31"
    ]
};

export default schedule;
