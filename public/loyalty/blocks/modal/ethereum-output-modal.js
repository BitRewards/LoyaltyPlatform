'use strict';

export default class EthereumOutputModal {
  constructor(el) {
    this.el = el;
    this.submitButton = document.querySelector('.js-btw-out');

    this.unlockForm = this.unlockForm.bind(this);

    $('body').on('RECAPTCHA_SUCCESS', this.unlockForm);
    document.body.addEventListener('ETHEREUM_OUTPUT_SUCCESS', this.success.bind(this));
  }

  unlockForm() {
    this.submitButton.disabled = false;
  }

  success(e) {
    const { url, tab } = e.detail.data;
    RewardManager.displayUsageModal(url);
    if (tab) {
      window.loyalty.tabManager.openTabByID(tab);
    }
  }
}
