/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import Vue from 'vue';
import VueRouter from 'vue-router';
import axios from 'axios';
import Util from './util';
import App from './App.vue'

require('./i18n');
window.TRANSLATION['ru'] =  require('./translation/ru.js');
window.TRANSLATION['tr'] = require('./translation/tr.js');
window.TRANSLATION['cn'] = require('./translation/cn.js');

Vue.prototype.__ = window.__;
Vue.prototype.tr = window.tr;
Vue.prototype.$http = axios;

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the body of the page. From here, you may begin adding components to
 * the application, or feel free to tweak this setup for your needs.
 */

import HomePage from './components/HomePage.vue';
import ViewClient from './components/ViewClient.vue';
import CreateClient from './components/CreateClient.vue';
import ProcessOrder from './components/ProcessOrder.vue';
import Spend from './components/Spend.vue';
import Confirm from './components/Confirm.vue';
import Countdown from './components/Countdown.vue';
import History from './components/History.vue';
import ViewTransaction from './components/ViewTransaction.vue';
import GiveBonus from './components/GiveBonus.vue';
import ListClients from './components/ListClients.vue';
import AcquireCode from './components/AcquireCode.vue';
import CouponView from './components/CouponView.vue';


window.STATE = {
    apiToken: localStorage['crm_api_token'] || Util.getParameterByName('api_token'),
    loading: 0
};

const routes = [
    { path: '/', component: HomePage},
    { path: '/list-clients', component: ListClients},
    { path: '/client-create', component: CreateClient},
    { path: '/coupon/:token', component: CouponView},
    { path: '/client/:userKey', component: ViewClient},
    { path: '/client/:userKey/acquire-code', component: AcquireCode},
    { path: '/client/:userKey/give-bonus', component: GiveBonus},
    { path: '/client/:userKey/history', component: History},
    { path: '/client/:userKey/process-order', component: ProcessOrder},
    { path: '/client/:userKey/spend', component: Spend},
    { path: '/client/:userKey/coupon/:token', component: CouponView},
    { path: '/client/:userKey/confirm/:rewardId', component: Confirm},
    { path: '/client/:userKey/transaction/:transactionId', component: ViewTransaction},
];

Vue.use(VueRouter);

window.router = new VueRouter({
    routes,
});

window.app = new Vue({
    router: window.router,
    data: {
      state: window.STATE,
      currentClient: null,
      lastOrderTotal: null
    },
    methods: {
      refreshCurrentClient(data) {
        if (data) {
          return this.currentClient = data;
        }

        Util.queryApi('GET', `users/${this.$route.params.userKey}`, {}, {}, data => {
          this.currentClient = data;
        });
      },
    },
    created() {
        if (this.$route.params.userKey) {
          this.refreshCurrentClient();
        }
    },
    render: h => h(App)
}).$mount('#app-container');
