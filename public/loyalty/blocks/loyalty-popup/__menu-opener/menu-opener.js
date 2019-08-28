'use strict';

import Util from 'utils';

export default class MenuOpener {
  constructor(el) {
    this.el = el;

    this.onClick = this.onClick.bind(this);

    this.el.addEventListener('click', this.onClick);
  }

  onClick(e) {
    e.preventDefault();
    Util.dispatchEvent('SIDEMENUOPENER_CLICK');
  }
}
