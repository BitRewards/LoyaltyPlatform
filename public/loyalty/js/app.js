'use strict';

import Vue from 'vue'
import VeeValidate from 'vee-validate'
import VueClipboard from 'vue-clipboard2'
import ReferrerBalance from './components/referrer-balance'
import Dashboard from './components/dashboard.js'
import HowItWorkLink from './components/how-it-work-link'
import Invite from './components/invite'
import History from './components/history'
import Balance from './components/balance'
import Earn from './components/earn'
import Spend from './components/spend'
import CheckPost from './components/check-post'

Vue.use(VeeValidate)
Vue.use(VueClipboard)

new Vue({
  el: '#app',
  components: {
    ReferrerBalance,
    Dashboard,
    HowItWorkLink,
    Invite,
    History,
    Balance,
    Earn,
    Spend,
    CheckPost
  }
})

import 'jquery';
import intlTelInput from 'intl-tel-input';

import LoyaltyPopup from 'loyalty-popup/loyalty-popup';


let loyalty = new LoyaltyPopup(document.querySelector('.js-loyalty-popup'))
loyalty.open();

let intPhone = $('.js-int-phone'),
    intPhoneInput = $('[name=int-phone]');

intPhone.intlTelInput({
  initialCountry: window.LANGUAGE == 'en' ? 'us' : 'ru',
});

let reset = function($el) {
  $(this).removeClass('is-error');
  intPhoneInput.val('');
};

$(document).on('blur', '.js-int-phone', function() {
  let $t = $(this);

  reset.call($t);

  if ($.trim($t.val())) {
    if ($t.intlTelInput('isValidNumber')) {
      intPhoneInput.val($t.intlTelInput('getNumber'));
    } else {
      $t.addClass('is-error');
    }
  }
});

$(document).on('keyup change', '.js-int-phone', reset);

import AjaxForm from 'ajax-form';
$('body').on('submit', '.js-ajax-form', function(e) {
  e.preventDefault();
  let ajaxForm = new AjaxForm($(this));
  ajaxForm.validateSend();
});

import 'form/login-form';
import 'form/confirm-form';
import 'form/reset-password-form';
import 'form/reset-password-confirm-phone-form';
import 'form/provide-phone-form';
import 'form/provide-email-form';
import 'form/confirmation-code';

import 'form/resend-code-form';
import 'form/partner-credentials';
import 'form/password-form';
import 'clipboard/clipboard';
import 'list/reward-list';
import 'form/withdraw-form';
import 'form/deposit-personal-eth-form';
import 'form/exchange-form'
import 'dropdown/dropdown'
import 'popover/popover-manager'
