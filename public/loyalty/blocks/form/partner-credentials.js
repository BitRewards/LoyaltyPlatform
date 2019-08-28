'use strict';

export default class CredentialsForm {
  constructor() {
    document.body.addEventListener('SUCCESS_ENTER_PARTNER_CREDENTIALS', this.ajaxSuccess.bind(this));
  }

  ajaxSuccess(e) {
    if (window.AUTH_METHOD == 'phone') {
      $('.js-hide-email-or-phone').val($('.js-enter-phone').val())
      window.loyalty.tabManager.openTabByID('confirmation-code-phone');
    } else {
      $('.js-hide-email-or-phone').val($('.js-enter-email').val());
      [].forEach.call(document.querySelectorAll('.js-confirm-email'), item => item.innerHTML = $('.js-enter-email').val());
      window.loyalty.tabManager.openTabByID('confirmation-code-email')
    }
  }
}

document.body.addEventListener('DOMContentLoaded', new CredentialsForm());

