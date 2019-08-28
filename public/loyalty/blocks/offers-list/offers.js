'use strict';

export default class Offers {
  constructor(el) {
    this.el = el;
    this.items = this.el.querySelectorAll('.js-offer');

    this.animationOn = this.animationOn.bind(this);
    this.animationOff = this.animationOff.bind(this);

    document.body.addEventListener('OFFER_ANIMATEON', this.animationOn);
    document.body.addEventListener('OFFER_ANIMATEOFF', this.animationOff);
  }

  animationOn() {
    [].forEach.call(this.items, item => {
      let timeline = item.querySelector('.js-offer-timeline');
      if (timeline) {
        let value = +timeline.getAttribute('data-percent');
        if (value) {
          timeline.style.transform = `translateX(${-100 + value}%)`;
          timeline.style.transitionDuration = '2s';
        }
      }
    });
  }

  animationOff() {
    [].forEach.call(this.items, item => {
        let timeline = item.querySelector('.js-offer-timeline');
        if (timeline) {
          timeline.style.transform = 'translateX(-100%)';
          timeline.style.transitionDuration = '0s';
        }
    });
  }
}
