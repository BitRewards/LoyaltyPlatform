'use strict';

import TagIt from 'tags/tagit';

export default class InviteModal {
  constructor(el) {
    this.el = el;
    this.slider = this.el.querySelector('.js-invite-modal-steps');
    this.sendEmail = this.el.querySelector('.js-invite-emails-sent-to');
    this.tagit = new TagIt(this.el.querySelector('.js-tagit'));

    this.reset = this.reset.bind(this);

    document.body.addEventListener('INVITE_FORM_SUCCESS', this.showSuccessScreen.bind(this));
  }

  showSuccessScreen(e) {
    this.slider.style.transform = 'translateX(-100%)';
    this.sendEmail.innerHTML = e.detail.data.email;

    setTimeout(() => {
      window.loyalty.modalManager.closeAll();
      setTimeout(() => {
        this.reset();
      }, 1000);
    }, 3000);
  }

  reset() {
    this.tagit.reset();
    this.slider.style.transform = '';
  }

}
