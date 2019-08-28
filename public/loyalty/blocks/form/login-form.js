'use strict';

import tracker from 'tracker'

export default class LoginForm {
  constructor(el) {
    this.el = el
    this.email = this.el.querySelector('.js-email-or-phone')

    this.email.addEventListener('change', this.onChangeEmail.bind(this))
    document.body.addEventListener('LOGIN_FORM_BEFORE_SEND', this.beforeSend)
    document.body.addEventListener('LOGIN_FORM_SUCCESS', this.ajaxSuccess.bind(this))
    document.body.addEventListener('LOGIN_FORM_SEND_ERROR', this.onError)
  }

  ajaxSuccess(e) {
    window.loyalty.fillAllEmailOrPhoneInputs();
    window.loyalty.openPasswordRequest(e.detail.data);
    tracker.trackGaEvent('Auth', 'successAuthStatus')
  }

  onChangeEmail() {
    if (this.email.value) {
      tracker.trackGaEvent('Auth', 'emailAuthFill')
    }
  }

  beforeSend() {
    tracker.trackGaEvent('Auth', 'passAuthButtonClick')
  }

  onError() {
    tracker.trackGaEvent('Auth', 'unsuccessAuthStatus')
  }
}

let form = document.querySelector('.js-login-form')
form && document.body.addEventListener('DOMContentLoaded', new LoginForm(form));
