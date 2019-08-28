'use strict';

export default class Overlay {
  constructor() {
    this.el = document.querySelector('.js-overlay');
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
