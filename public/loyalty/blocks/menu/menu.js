'use strict';

import Util from 'utils';

export default class Menu {
  constructor(el) {
    this.el = el;
    this.activeClass = 'is-active';
    this.currentItem = null;

    this.onClick = this.onClick.bind(this);
    this.selectMenuItem = this.selectMenuItem.bind(this);
    this.syncWithTab = this.syncWithTab.bind(this);

    this.el.addEventListener('click', this.onClick);
    document.body.addEventListener('TAB_BEFORE_OPENED', this.syncWithTab);
  }

  onClick(e) {
    let target = e.target.closest('.js-menu-item');
    if (!target) return;

    e.preventDefault();

    let id = target.getAttribute('data-id');

    Util.dispatchEvent('MENU_SELECT', {id: id});
  }

  selectMenuItem(id) {
    if (this.currentItem) {
        this.currentItem.classList.remove(this.activeClass);
    }

    let target = document.querySelector(`.js-menu-item[data-id="${id}"]`);
    if (target) {
      target.classList.add(this.activeClass);
      this.currentItem = target;
    }
  }

  syncWithTab(e) {
    if (e.detail.isAuth) {
      this.selectMenuItem('login');
    } else {
      this.selectMenuItem(e.detail.id);
    }
  }
}
