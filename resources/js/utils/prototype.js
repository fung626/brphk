import Vue from "vue";

Vue.prototype.$appName = process.env.APP_NAME;
Vue.prototype.$env = process.env.APP_ENV;
Vue.prototype.$url = process.env.APP_URL;
Vue.prototype.$momentDateFormat = "dddd, Do MMMM YYYY";

String.prototype.capitalize = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
};

Number.prototype.format = function(n, x) {
    // n.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,')
    const re = "\\d(?=(\\d{" + (x || 3) + "})+" + (n > 0 ? "\\." : "$") + ")";
    return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, "g"), "$&,");
};

Number.prototype.abbreviateAmount = function() {
    let isNegative = false;
    let num = this;
    let formattedNumber = 0;
    if (num < 0) {
        isNegative = true;
    }
    num = Math.abs(num);
    if (num >= 1000000000) {
        formattedNumber =
            (num / 1000000000).toFixed(1).replace(/\.0$/, "") + "G";
    } else if (num >= 1000000) {
        formattedNumber = (num / 1000000).toFixed(1).replace(/\.0$/, "") + "M";
    } else if (num >= 1000) {
        formattedNumber = (num / 1000).toFixed(1).replace(/\.0$/, "") + "K";
    } else {
        formattedNumber = num;
    }
    if (isNegative) {
        formattedNumber = "-" + formattedNumber;
    }
    return formattedNumber;
};
