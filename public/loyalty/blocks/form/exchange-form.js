'use strict';

import FormValidate from 'form-validate';
import Util from 'utils';

export default class ExchangeForm {
  constructor(el) {
    this.el = el;

    this.onSubmit = this.onSubmit.bind(this);

    this.el.addEventListener('submit', this.onSubmit);
  }

  onSubmit(e) {
    // e.preventDefault();
    let form = new FormValidate(this.el);
    // if (form.validate()) {
    //   Util.dispatchEvent('EXCHANGE_MODAL_NEXT');
    // }
    if (!form.validate()) {
      e.preventDefault();
    }
  }
}

let form = document.querySelector('.js-exchange-form');
form && new ExchangeForm(form);
