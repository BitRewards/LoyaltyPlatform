'use strict';

import Util from 'utils';

export default class Sidemenu {
  constructor(el) {
    this.el = el;
    this.showClass = 'is-show';
    this.animatedClass = 'is-animatable';

    this.startX = 0;
    this.currentX = 0;
    this.touchingSideNav = false;

    this.open = this.open.bind(this);
    this.close = this.close.bind(this);
    this.onTouchStart = this.onTouchStart.bind(this);
    this.onTouchEnd = this.onTouchEnd.bind(this);
    this.onTransitionEnd = this.onTransitionEnd.bind(this);
    this.onTouchMove = this.onTouchMove.bind(this);
    this.update = this.update.bind(this);

    document.addEventListener('touchstart', this.onTouchStart);
    document.addEventListener('touchmove', this.onTouchMove);
    document.addEventListener('touchend', this.onTouchEnd);
  }

  open() {
    this.el.classList.add(this.showClass);
    this.el.addEventListener('transitionend', this.onTransitionEnd);
    Util.dispatchEvent('SIDEMENU_OPEN');
  }

  close() {
    this.el.classList.remove(this.showClass);
    this.el.removeEventListener('transitionend', this.onTransitionEnd);
    Util.dispatchEvent('SIDEMENU_CLOSE');
  }

  onTransitionEnd (e) {
    this.el.classList.remove(this.animatedClass);
    this.el.removeEventListener('transitionend', this.onTransitionEnd);
  }

  onTouchStart(e) {
    if (!this.el.classList.contains(this.showClass)) return;

    this.startX = e.touches[0].pageX;
    this.currentX = this.startX;

    this.touchingSideNav = true;
    requestAnimationFrame(this.update);
  }

  onTouchMove (e) {
    if (!this.touchingSideNav) return;

    this.currentX = e.touches[0].pageX;
    const translateX = Math.min(0, this.currentX - this.startX);

    if (translateX < 0) {
      e.preventDefault();
    }
  }

  onTouchEnd (e) {
    if (!this.touchingSideNav) return;

    this.touchingSideNav = false;

    const translateX = Math.min(0, this.currentX - this.startX);
    this.el.style.transform = '';

    if (translateX < 0) {
      this.close();
    }
  }

  update () {
    if (!this.touchingSideNav) return;

    requestAnimationFrame(this.update);

    const translateX = Math.min(0, this.currentX - this.startX);
    this.el.style.transform = `translateX(${translateX}px)`;
  }
}
