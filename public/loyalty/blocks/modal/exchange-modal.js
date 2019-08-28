export default class ExchangeModal {
  constructor() {
    this.el = document.querySelector('.js-exchange-modal');
    if (!this.el) {
        return
    }
    this.steps = this.el.querySelector('.js-exchange-steps');
    this.slider = this.el.querySelector('.js-exchange-slider');
    this.sliderNextClass = 'is-next';
    this.ethAddress = this.el.querySelector('.js-exchange-eth');
    this.ethFrom = this.el.querySelector('.js-exchange-from');

    this.slideNext = this.slideNext.bind(this);
    this.onStepsClick = this.onStepsClick.bind(this);
    this.onSuccess = this.onSuccess.bind(this);
    this.reset = this.reset.bind(this);

    this.steps.addEventListener('click', this.onStepsClick);
    document.body.addEventListener('EXCHANGE_MODAL_NEXT', this.slideNext);
    document.body.addEventListener('EXCHANGE_SUCCESS', this.onSuccess);
    document.body.addEventListener('EXCHANGE_MODAL_RESET', this.reset);
  }

  slideNext() {
    this.slider.classList.add(this.sliderNextClass);
    this.steps.classList.add(this.sliderNextClass);
    this.ethFrom.value = this.ethAddress.value;
  }

  reset() {
    this.slider.classList.remove(this.sliderNextClass);
    this.steps.classList.remove(this.sliderNextClass);
    // this.ethFrom.value = this.ethAddress.value = '';
  }

  onStepsClick(e) {
    e.preventDefault();

    let target = e.target.closest('.steps__link');

    if (target) {
      if (target.getAttribute('data-step') == '1') {
        this.slider.classList.remove(this.sliderNextClass);
        this.steps.classList.remove(this.sliderNextClass);
      } else {
        this.slider.classList.add(this.sliderNextClass);
        this.steps.classList.add(this.sliderNextClass);
      }
    }
  }

  onSuccess(e) {
    window.loyalty.modalManager.closeAll()
  }
}
