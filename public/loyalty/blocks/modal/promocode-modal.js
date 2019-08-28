'use strict';

export default class PromoCodeModal {
  constructor(el) {
    this.el = el;
    this.slider = this.el.querySelector('.js-promocode-modal-steps');
    this.message = this.el.querySelector('.js-promocode-modal-success-text');
    this.title = this.el.querySelector('.js-promocode-modal-title');


    document.body.addEventListener('PROMOCODE_SUCCESS', this.showSuccessScreen.bind(this));
  }

  showSuccessScreen(e) {
    this.title.classList.add('is-hide');
    this.slider.style.transform = 'translateX(-100%)';
    if (e.detail && e.detail.data && e.detail.data.message) {
      this.message.innerHTML = e.detail.data.message;
    } else {
      this.message.innerHTML = 'Вы получили бонус';
    }

    setTimeout(() => {
      window.loyalty.modalManager.closeAll();
      loyalty.updateEverything();
      setTimeout(() => {
        this.reset();
      }, 1000);
    }, 3000);
  }

  reset() {
    this.el.querySelector('[name="promocode"]').value = '';
    this.slider.style.transform = '';
  }
}
