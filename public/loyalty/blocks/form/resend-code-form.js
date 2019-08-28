'use strict';

export default class ResendCodeForm {
	constructor() {
		document.body.addEventListener('RESEND_CODE_SUCCESS', this.ajaxSuccess.bind(this));
	}

	ajaxSuccess(e) {
		window.loyalty.fillAllEmailOrPhoneInputs();
		window.loyalty.modalManager.openModal({ detail: {
				modalName: '.js-resend-code-modal',
			}});

		window.loyalty.openPasswordRequest(e.detail.data);
	}
}

document.body.addEventListener('DOMContentLoaded', new ResendCodeForm());

