'use strict';

export default class ConfirmationCodeForm {
  constructor() {
    document.body.addEventListener('CONFIRMATION_CODE_SUCCESS', this.ajaxSuccess.bind(this));
  }

  ajaxSuccess(e) {
    if (window.IS_USER_AUTH && !window.USER_HAS_CONFIRMED_PARTNER_CREDENTIALS) {
      document.location = document.location.origin + document.location.pathname;
    } else {
      window.loyalty.tabManager.openTabByID('create-password');
    } 
  }
}

document.body.addEventListener('DOMContentLoaded', new ConfirmationCodeForm());
