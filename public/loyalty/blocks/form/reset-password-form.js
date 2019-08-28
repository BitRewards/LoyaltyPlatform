'use strict';

export default class ResetPasswordForm {
  constructor() {
    document.body.addEventListener('RESET_PASSWORD_FORM_SUCCESS', this.ajaxSuccess.bind(this));
  }

  ajaxSuccess(e) {
    window.location.href = window.URLS.INDEX;
  }
}

document.body.addEventListener('DOMContentLoaded', new ResetPasswordForm());
