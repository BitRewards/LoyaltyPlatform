import Vue from 'vue'
import VueRouter from 'vue-router'
import swal from 'sweetalert'
import axios from 'axios'


export default class Util {

    static getParameterByName(name, url) {
        if (!url) {
            url = window.location.href;
        }
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    };

    static queryApi(method, path, params = {}, body = {}, callback, callbackAlways) {
        method = method.toLowerCase();

        let options = null, promise;
        let url = '/api/' + path;
        if (method != 'get') {
            url += '?api_token=' + window.STATE.apiToken;
            promise = axios[method](url, body, {   emulateJSON: true});
        } else {
            let options = {
                params: $.extend({api_token: window.STATE.apiToken}, params),
                body: body
            };
            promise = axios[method](url, options);
        }
        window.STATE.loading++;
        promise.then(function(response){
            window.STATE.loading--;
            if (callbackAlways) {
                callbackAlways();
            }
            if (typeof response.data == 'string') {
                response.data = JSON.parse(response.data);
            }
            callback(response.data ? response.data.data : null);
        }, function(response){
            window.STATE.loading--;
            if (callbackAlways) {
                callbackAlways();
            }
            if (typeof response.data == 'string') {
                response.data = JSON.parse(response.data);
            }

            let handled = false;
            if (response && response.data && response.data.data) {
                if (Util.displayErrors(response.data.data)) {
                    handled = true;
                }
            }

            if (!handled) {
                swal('Ooops!', 'An error occurred, please refresh the page', 'error');
            }
        });
    };

    static displayErrors(data) {
        let errorsToAlert = [];
        for (let field in data) {
            errorsToAlert.push(data[field].join ? data[field].join("\n") : data[field]);
        }
        console.log(data);
        if (errorsToAlert.length) {
            var errorsStr = errorsToAlert.join("\n");
            swal('', errorsStr, 'error');
            return true;
        }
        return false;
    };
}
