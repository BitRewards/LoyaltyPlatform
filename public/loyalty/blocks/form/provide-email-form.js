'use strict';

export default class ProvideEmailForm {
    constructor() {
        document.body.addEventListener('PROVIDE_EMAIL_FORM_NEED_MERGE', this.ajaxSuccess.bind(this));
    }

    ajaxSuccess(e) {
        let email = $('.js-provide-email').val();
        window.loyalty.mergeProfiles(email);
    }
}

document.body.addEventListener('DOMContentLoaded', new ProvideEmailForm());
