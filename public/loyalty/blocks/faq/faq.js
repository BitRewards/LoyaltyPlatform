'use strict';

import Util from 'utils';

export default class Faq {
  constructor(el) {
    this.el = el;
    this.current = this.el.querySelector('.js-faq.is-active');

    this.activeClass = 'is-active';

    this.onClick = this.onClick.bind(this);

    this.el.addEventListener('click', this.onClick);
  }

  onClick(e) {
    let target = e.target.closest('.js-faq');

    if (!target) return;

    if (target.classList.contains(this.activeClass)) {
      target.classList.remove(this.activeClass);
    } else {
      if (this.current) {
        this.current.classList.remove(this.activeClass);
      }

      target.classList.add(this.activeClass);
      this.current = target;
    }

    let handler = () => {
      target.removeEventListener(Util.whichTransitionEvent(), handler);
    }
    target.addEventListener(Util.whichTransitionEvent(), handler);
  }
}
