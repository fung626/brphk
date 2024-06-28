import Vue from "vue";
import VueI18n from "vue-i18n";

Vue.use(VueI18n);

// const { locale, translations } = window.navigator;

// const i18n = new VueI18n({
//     locale,
//     messages: {
//         [locale]: translations
//     }
// });

const i18n = new VueI18n({
    locale: "tc",
    messages: {
        en: {
            auth: {
                signin: {
                    msg: "Sign In to your account"
                },
                forgotpassword: {
                    title: "Forgot your password?",
                    msg:
                        "Enter the email address associated with your account and we will send you a link to reset your password."
                },
                resetpassword: {
                    title: "Reset Password",
                    msg: "reset your new password."
                }
            },
            http: {
                success: {
                    login: "Successfully Login",
                    resetpwd: "Successfully reset password"
                },
                fail: {
                    login: "Incorrect email or password",
                    resetpwd: "Reset password failed"
                }
            },
            login: "Login",
            logout: "Logout",
            owner: "Owner",
            chinese: "Chinese",
            english: "English",
            notification: "Notification",
            updatedby: "Updated by",
            name: "Name",
            phone: "Phone",
            email: "Email",
            address: "Address",
            forgotpassword: "Forgot Password",
            oldpassword: "Old Password",
            newpassword: "New Password",
            confirmpassword: "Confirm Password",
            resetpassword: "Reset Password",
            password: "Password",
            table: "Table",
            profile: "Profile",
            settings: "Settings",
            management: "Management",
            chart: "Chart",
            home: "Home",
            dashboard: "Dashboard",
            item: "Item",
            filter: "Filter",
            option: "Option",
            income: "Income",
            expenditure: "Expenditure",
            role: "Role",
            user: "User",
            debt: "Debt",
            venue: "Venue",
            receivable: "Receivable",
            payable: "Payable",
            bank: "Bank",
            account: "Account",
            payment: "Payment",
            summary: "Summary",
            company: "Company",
            secretary: "Secretary",
            cashflow: "Cash Flow",
            expenses: "Expenses",
            internaltransfer: "Internal Transfer",
            expectedexpenses: "Expected Expenses",
            profit: "Profit",
            years: "Years",
            month: "Month",
            date: "Date",
            debtdate: "Debt Date",
            duedate: "Due Date",
            paiddate: "Paid Date",
            createdat: "Created at",
            updatedat: "Updated at",
            actions: "Actions",
            paid: "Paid",
            amount: "Amount",
            balance: "Balance",
            tax: "Tax",
            total: "Total",
            type: "Type",
            remark: "Remark",
            calendar: "Calendar",
            eventcolor: "Event Color",
            cheque: {
                bank: "Cheque Bank",
                no: "Cheque No.",
                issuer: "Cheque Issuer",
                signer: "Signer",
                payto: "Pay to",
                issued: {
                    company: "Issued Company",
                    date: "Issued Date"
                }
            },
            rent: {
                title: "Rent",
                arrears: "Rent Arrears",
                property: {
                    name: "Property",
                    owner: "Property Owner"
                },
                amount: "Rent",
                tenant: "Tenant",
                managementfee: "Management Fee",
                rates: "Rates",
                rentpersquarefoot: "Rent Per Square Foot",
                governmentrent: "Government Rent",
                otherfee: "Other Fee",
                area: "Area",
                startdate: "Start Date",
                fttdate: "Fix Term Tenancy Date",
                bcdate: "Break Clause Date",
                month: "Month",
                date: "Date"
            },
            create: "Create",
            info: "Info",
            details: "Details",
            search: "Search",
            admin: "Admin",
            ADMIN: "ADMIN",
            employee: "Employee",
            EMPLOYEE: "EMPLOYEE",
            traffic: "Traffic",
            radio: {
                yes: "Yes",
                no: "No"
            },
            button: {
                jumpto: "Jump to",
                selectall: "Select All",
                submit: "Submit",
                confirm: "Confirm",
                reset: "Reset",
                update: "update",
                edit: "Edit",
                add: "Add",
                delete: "Delete",
                cancel: "Cancel",
                clear: "Clear",
                close: "Close"
            },
            alert: {
                title: "Alert",
                update: "Are you sure you want to update this record?",
                delete: "Are you sure you want to delete this record?",
                shipping: "Please enter unit"
            },
            snackbar: {
                fail: {
                    login: "Fail to login",
                    update: "Fail to update",
                    create: "Fail to create",
                    delete: "Fail to delete"
                },
                success: {
                    login: "Successfully Login",
                    updated: "Successfully Updated",
                    created: "Successfully Created",
                    deleted: "Successfully deleted"
                }
            },
            schedule: {
                title: "Schedule",
                daily: "Daily",
                Daily: "Daily",
                weekly: "Weekly",
                Weekly: "Weekly",
                monthly: "Monthly",
                Monthly: "Monthly",
                yearly: "Yearly",
                Yearly: "Yearly",
                specific: "Specific",
                Specific: "Specific",
                Monday: "Monday",
                Tuesday: "Tuesday",
                Wednesday: "Wednesday",
                Thursday: "Thursday",
                Friday: "Friday",
                Saturday: "Saturday",
                Sunday: "Sunday"
            },
            hit: {
                comma: "Separate keywords with an English comma",
                params:
                    "Please asure all the data is correctly entered or selected"
            },
            error: {
                nodata: "No data found"
            }
        },
        tc: {
            auth: {
                signin: {
                    msg: "登錄到您的帳戶"
                },
                forgotpassword: {
                    title: "忘記了您的密碼?",
                    msg:
                        "輸入與您的帳戶電子郵件地址，我們將向您發送一個鏈接以重置您的密碼。"
                },
                resetpassword: {
                    title: "重置密碼",
                    msg: "輸入您的新密碼"
                }
            },
            http: {
                success: {
                    login: "成功登入",
                    resetpwd: "成功更改密碼"
                },
                fail: {
                    login: "錯誤的郵箱帳號或密碼",
                    resetpwd: "更改密碼失敗"
                }
            },
            login: "登入",
            logout: "登出",
            owner: "負責人",
            chinese: "中文",
            english: "英文",
            notification: "通知",
            updatedby: "更新用戶",
            name: "名稱",
            phone: "電話",
            email: "電郵",
            address: "地址",
            forgotpassword: "忘記密碼",
            oldpassword: "舊密碼",
            newpassword: "新密碼",
            confirmpassword: "確認密碼",
            resetpassword: "重置密碼",
            password: "密碼",
            table: "清單",
            profile: "我的帳戶",
            settings: "設定",
            management: "管理",
            chart: "圖表",
            home: "主頁",
            dashboard: "控制板",
            item: "項目",
            filter: "篩選",
            option: "選項",
            income: "收入",
            expenditure: "支出",
            role: "權限",
            user: "用戶",
            debt: "應收與應付帳",
            venue: "場地",
            receivable: "應收",
            payable: "應付",
            bank: "銀行",
            account: "戶口",
            payment: "支付",
            summary: "總結",
            company: "公司",
            secretary: "秘書",
            cashflow: "現金流",
            expenses: "支出",
            internaltransfer: "內部轉帳",
            expectedexpenses: "預計支出",
            profit: "盈利",
            years: "年度",
            month: "月份",
            date: "日期",
            debtdate: "應收日期",
            duedate: "到期日期",
            paiddate: "已付日期",
            startdate: "起始日期",
            enddate: "結束日期",
            createdat: "新增日期",
            updatedat: "更新日期",
            actions: "功能",
            paid: "已付",
            amount: "金額",
            balance: "結餘",
            tax: "税",
            total: "總額",
            type: "類型",
            remark: "備註",
            calendar: "日曆",
            eventcolor: "項目顏色",
            cheque: {
                bank: "支票銀行",
                no: "支票號碼",
                issuer: "開票人",
                signer: "簽票人",
                payto: "收款人",
                issued: {
                    company: "開票公司",
                    date: "日期"
                }
            },
            rent: {
                title: "收租",
                arrears: "欠租",
                property: {
                    name: "物業",
                    owner: "物業擁有人"
                },
                amount: "租金",
                tenant: "租客",
                managementfee: "管理費",
                rates: "差餉",
                rentpersquarefoot: "呎租",
                governmentrent: "地租",
                otherfee: "其他支出",
                area: "面積",
                startdate: "起租日",
                fttdate: "梗約",
                bcdate: "生約",
                month: "月份",
                date: "日期"
            },
            create: "新增",
            info: "詳細",
            details: "詳細",
            search: "搜尋",
            admin: "管理員",
            ADMIN: "管理員",
            employee: "員工",
            EMPLOYEE: "員工",
            traffic: "流量",
            radio: {
                yes: "是",
                no: "否"
            },
            button: {
                jumpto: "跳至",
                selectall: "全選",
                submit: "提交",
                confirm: "確定",
                reset: "重置",
                update: "更新",
                edit: "更改",
                add: "新增",
                delete: "刪除",
                cancel: "取消",
                clear: "清除",
                close: "關閉"
            },
            alert: {
                title: "提示",
                update: "您確定要更新此記錄嗎？",
                delete: "您確定要刪除此記錄嗎？",
                shipping: "請輸入出貨數量"
            },
            snackbar: {
                fail: {
                    login: "登入失敗",
                    update: "更新記錄失敗",
                    create: "新增記錄失敗",
                    delete: "刪除記錄失敗"
                },
                success: {
                    login: "成功登入",
                    updated: "成功更新記錄",
                    created: "成功新增記錄",
                    deleted: "成功刪除記錄"
                }
            },
            schedule: {
                title: "定期",
                daily: "每日",
                Daily: "每日",
                weekly: "每週",
                Weekly: "每週",
                monthly: "每月",
                Monthly: "每月",
                yearly: "每年",
                Yearly: "每年",
                specific: "特定日子",
                Specific: "特定日子",
                Monday: "週一",
                Tuesday: "週二",
                Wednesday: "週三",
                Thursday: "週四",
                Friday: "週五",
                Saturday: "週六",
                Sunday: "週日"
            },
            hit: {
                comma: "用英文逗號分隔關鍵字",
                params: "請確定所有資料都填寫或選擇"
            },
            error: {
                nodata: "沒有找到記錄"
            }
        }
    }
});

export default i18n;
