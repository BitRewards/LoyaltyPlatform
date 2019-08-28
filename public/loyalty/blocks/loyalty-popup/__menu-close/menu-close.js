'use strict';

import Util from 'utils';

export default class MenuClose {
  constructor(el) {
    this.el = el;

    this.onClick = this.onClick.bind(this);

    this.el.addEventListener('click', this.onClick);
  }

  onClick(e) {
    e.preventDefault();

    Util.dispatchEvent('SIDEMENUCLOSE_CLICK');
  }
}
