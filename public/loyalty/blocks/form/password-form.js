'use strict';

import tracker from 'tracker'

export default class PasswordForm {
  constructor() {
    this.password = document.querySelector('.js-new-password')

    this.password.addEventListener('change', this.trackPassFill.bind(this))
    document.body.addEventListener('PASSWORD_FORM_BEFORE_SEND', this.beforeSend)
    document.body.addEventListener('PASSWORD_FORM_SUCCESS', this.ajaxSuccess.bind(this))
    document.body.addEventListener('NEWPASS_TRACK_EVENT', this.trackEvent)
    document.body.addEventListener('PASSWORD_FORM_SEND_ERROR', this.onError)
  }

  ajaxSuccess(e) {
    document.location = document.location.origin + document.location.pathname;

    tracker.trackGaEvent('Registration', 'successRegistrationStatus')
  }

  trackEvent() {
    if (!window.IS_USER_AUTH) {
      tracker.trackGaEvent('Registration', 'newpassPageShow')
    }
  }

  beforeSend() {
    tracker.trackGaEvent('Registration', 'newpassRegButtonClick')
  }

  trackPassFill() {
    if (this.password.value) {
      tracker.trackGaEvent('Registration', 'newpassPassFill')
    }
  }

  onError() {
    tracker.trackGaEvent('Registration', 'unsuccessRegistrationStatus')
  }
}

document.body.addEventListener('DOMContentLoaded', new PasswordForm());
