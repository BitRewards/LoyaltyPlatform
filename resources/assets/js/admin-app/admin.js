import Vue from 'vue'
import axios from 'axios'

require('../i18n')
window.TRANSLATION['ru'] =  require('./translation/ru.js')

Vue.prototype.__ = window.__;
Vue.prototype.tr = window.tr;
Vue.prototype.$http = axios;

import PartnerCustomizations from './components/customizations/PartnerCustomizations.vue'

const csrfToken = window.jQuery('meta[name="csrf-token"]').first().attr('content')

axios.defaults.headers['X-CSRF-Token'] = csrfToken;

const app = new Vue({
    el: '#admin-app',
    components: {
        PartnerCustomizations,
    }
})
