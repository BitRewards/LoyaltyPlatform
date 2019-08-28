'use strict';

export default class ResetPasswordConfirmPhoneForm {
    constructor() {
        document.body.addEventListener('RESET_PASSWORD_CONFIRM_PHONE_FORM', this.ajaxSuccess.bind(this));
    }

    ajaxSuccess(e) {
        $('.js-reset-password-token').val($('.js-sms-token').val());
        window.loyalty.tabManager.openTabByID('reset-password');
    }
}

document.body.addEventListener('DOMContentLoaded', new ResetPasswordConfirmPhoneForm());
