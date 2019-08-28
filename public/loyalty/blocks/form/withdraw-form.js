'use strict';

import FormValidate from 'form-validate';

export default class WithdrawForm {
  constructor(el) {
    this.el = el;

    this.onSubmit = this.onSubmit.bind(this);

    this.el.addEventListener('submit', this.onSubmit);
  }

  onSubmit(e) {
    e.preventDefault();

    let form = new FormValidate(this.el);
    if (form.validate()) { 
      window.loyalty.modalManager.openModal({ detail: { modalName: '.js-withdraw-modal' } });
    }
  }
}

let form = document.querySelector('.js-withdraw-form');
form && new WithdrawForm(form);
