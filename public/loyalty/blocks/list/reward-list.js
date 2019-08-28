'use strict';

export default class RewardList {
  constructor(el) {
    this.el = el;
    this.newClass = 'is-new';

    this.removeNewSticker = this.removeNewSticker.bind(this);

    [].forEach.call(this.el.querySelectorAll('.js-reward-list-item.is-new'), item => {
      item.addEventListener('mouseenter', this.onMouseEnter.bind(this, item));
    });
    document.body.addEventListener('BALANCE_CLOSE', this.removeNewSticker);
  }

  onMouseEnter(item) {
    item.classList.remove(this.newClass);
  }

  removeNewSticker() {
    [].forEach.call(this.el.querySelectorAll(`.${ this.newClass }`), item => item.classList.remove(this.newClass));
  }
}

document.addEventListener('DOMContentLoaded', () => {
  if (document.querySelector('.js-reward-list')) {
    new RewardList(document.querySelector('.js-reward-list'));
  }
})
