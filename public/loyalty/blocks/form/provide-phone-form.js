'use strict';

export default class ProvidePhoneForm {
    constructor() {
        document.body.addEventListener('PROVIDE_PHONE_FORM_NEED_MERGE', this.ajaxSuccess.bind(this));
    }

    ajaxSuccess(e) {
        let phone = $('.js-provide-phone').val();
        window.loyalty.mergeProfiles(phone);
    }
}

document.body.addEventListener('DOMContentLoaded', new ProvidePhoneForm());
