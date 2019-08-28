'use strict';

class PopoverManager {
  constructor() {
    this.currentOpenPopover = null;
    this.onClick = this.onClick.bind(this);
  
    document.body.addEventListener('click', this.onClick);
  }

  onClick(e) {
    let target = e.target.closest('.js-show-popover');

    if (target) {
      e.preventDefault();

      this.currentOpenPopover = document.querySelector(target.dataset.popover);
      this.currentOpenPopover.classList.toggle('is-show');
    } else {
      if (this.currentOpenPopover) {
        this.currentOpenPopover.classList.remove('is-show');
      }
    }
  }
}

new PopoverManager();
