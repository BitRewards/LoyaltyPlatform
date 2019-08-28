'use strict';

import FormValidate from 'form-validate';
import Util from 'utils';

export default class DepositPersonalEthForm {
  constructor(el) {
    this.el = el;

    this.onSubmit = this.onSubmit.bind(this);

    this.el.addEventListener('submit', this.onSubmit);
  }

  onSubmit(e) {
    let form = new FormValidate(this.el);
    if (!form.validate()) {
      e.preventDefault();
      // Util.dispatchEvent('DEPOSIT_PERSONAL_NEXT');
    }
  }
}

let form = document.querySelector('.js-deposit-personal-eth-form');
form && new DepositPersonalEthForm(form);
