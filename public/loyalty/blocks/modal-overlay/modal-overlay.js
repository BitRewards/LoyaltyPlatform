'use strict';

export default class ModalOverlay {
  constructor() {
    this.el = document.querySelector('.js-modal-overlay');
    this.activeClass = 'is-active';
    
    this.show = this.show.bind(this);
    this.hide = this.hide.bind(this);
  }

  show() {
    this.el.classList.add(this.activeClass);
  }

  hide() {
    this.el.classList.remove(this.activeClass);
  }

}
