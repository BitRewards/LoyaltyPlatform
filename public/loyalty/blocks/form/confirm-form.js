'use strict';

export default class ConfirmForm {
    constructor() {
        document.body.addEventListener('CONFIRM_FORM_SUCCESS', this.ajaxSuccess.bind(this));
    }

    ajaxSuccess(e) {
        window.loyalty.tabManager.openTabByID('spend');
    }
}

document.body.addEventListener('DOMContentLoaded', new ConfirmForm());
