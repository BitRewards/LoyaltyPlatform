'use strict';

export default class LoyaltyPopupHeader {
  constructor(el) {
    this.el = el;
    this.shadowClass = 'has-shadow';
    this.sideMenuOpenClass = 'is-open-sidemenu';

    this.onOpenSidemenu = this.onOpenSidemenu.bind(this);
    this.onCloseSidemenu = this.onCloseSidemenu.bind(this);
    this.checkShadow = this.checkShadow.bind(this);

    document.body.addEventListener('SIDEMENU_OPEN', this.onOpenSidemenu);
    document.body.addEventListener('SIDEMENU_CLOSE', this.onCloseSidemenu);
    document.body.addEventListener('HEADER_SHADOW', this.checkShadow);
  }

  onOpenSidemenu() {
    this.el.classList.add(this.shadowClass);
    this.el.classList.add(this.sideMenuOpenClass);
  }

  onCloseSidemenu() {
    this.el.classList.remove(this.sideMenuOpenClass);
  }

  checkShadow(e) {
    e.detail.isShadow ? this.el.classList.add(this.shadowClass) : this.el.classList.remove(this.shadowClass);
  }
}
