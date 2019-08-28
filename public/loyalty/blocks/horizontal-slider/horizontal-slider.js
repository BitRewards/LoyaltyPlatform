export default class HorizontalSlider {
  constructor(el) {
    this.el = el;
    this.offset = 0;

    this.slideNext = this.slideNext.bind(this);
    this.slidePrev = this.slidePrev.bind(this);
    this.slideTo = this.slideTo.bind(this);
    this.reset = this.reset.bind(this);
  }

  slideNext() {
    this.offset += 100;
    this.el.style.transform = `translateX(-${this.offset}%)`;
  }

  slidePrev() {
    this.offset -= 100;
    this.el.style.transform = `translateX(-${this.offset}%)`;
  }

  slideTo(index) {
    this.offset = index * 100;
    this.el.style.transform = `translateX(-${this.offset}%)`;
  }

  reset() {
    this.el.style.transform = '';
  }
}
