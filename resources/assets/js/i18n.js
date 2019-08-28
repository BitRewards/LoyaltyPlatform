
window.LANGUAGE_DEFAULT = "en";
window.LANGUAGE = (window.LANGUAGE || window.LANGUAGE_DEFAULT);
window.LANGUAGE_EN = "en";
window.LANGUAGE_RU = "ru";

window.TRANSLATION = {
    "en": {},
    "ru": {}
};

window.__ = function() {
    function translate(str) {
        if (window.LANGUAGE == window.LANGUAGE_DEFAULT) {
            return str;
        }

        return window.TRANSLATION[window.LANGUAGE][str] || str;
    }

    var args = arguments;
    if (args.length == 1) {
        return translate(args[0]);
    }

    if (args.length == 2) {
        if (args[1] === null) {
            args[1] = '';
        } else {
            if (typeof args[1] != 'object') {
                var matches = args[0].match(/%[a-z_]+%/);
                if (matches) {
                    var tt = {};
                    tt[matches[0]] = args[1];
                    args[1] = tt;
                }
            }
        }

    }

    if (args.length == 2 && typeof args[1] == 'object') {
        var temp = {};
        for (var key in args[1]) {
            if (key[0] >= 'a' && key[0] <= 'z') {
                temp["%" + key + "%"] = args[1][key];
            } else {
                temp[key] = args[1][key];
            }
        }

        if (temp['%count%']) {
            var regex = /{([^|]+)\|([^|]+)(\|([^}]+)|)}/g;
            var count = temp['%count%'], form;

            if (LANGUAGE == LANGUAGE_RU) {
                form = count % 10 == 1 && count % 100 != 11 ? 1 : (count % 10 >= 2 && count % 10 <= 4 && (count % 100 < 10 || count % 100 >= 20) ? 2 : 4);
            } else {
                form = count > 1 ? 2 : 1;
            }

            args[0] = args[0].replace(regex, "$" + form, translate(args[0]));
        } else {
            args[0] = translate(args[0]);
        }

        for (var key in temp) {
            args[0] = args[0].replace(new RegExp(key, "g"), temp[key]);
        }
    } else {
        args[0] = translate(args[0]);
        for (var i = 1; i < args.length; i++) {
            args[0] = args[0].replace('%s', args[i]);
        }
        if (args.length ==1) {
            args[0] = args[0].replace(/\%s/g, '');
        }
    }

    return args[0].split("##")[0];
};

window.tr = window.__;