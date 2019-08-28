export default class DepositPersonalModal {
  constructor() {
    this.el = document.querySelector('.js-deposit-personal-modal');
    if (!this.el) {
        return
    }
    this.steps = this.el.querySelector('.js-deposit-personal-steps');
    this.slider = this.el.querySelector('.js-deposit-personal-slider');
    this.sliderNextClass = 'is-next';
    this.ethAddress = this.el.querySelector('.js-ethereum-wallet');
    this.ethInput = this.el.querySelector('.js-deposit-from');
    this.ethText = this.el.querySelector('.js-deposit-eth');

    this.slideNext = this.slideNext.bind(this);
    this.onStepsClick = this.onStepsClick.bind(this);
    this.reset = this.reset.bind(this);

    this.steps.addEventListener('click', this.onStepsClick);
    document.body.addEventListener('DEPOSIT_PERSONAL_NEXT', this.slideNext);
    document.body.addEventListener('DEPOSIT_PERSONAL_RESET', this.reset);
  }

  slideNext() {
    this.slider.classList.add(this.sliderNextClass);
    this.steps.classList.add(this.sliderNextClass);
    this.ethInput.value = this.ethText.innerHTML = this.ethAddress.value;
  }

  reset() {
    this.slider.classList.remove(this.sliderNextClass);
    this.steps.classList.remove(this.sliderNextClass);
    // this.ethInput.value = this.ethText.innerHTML = this.ethAddress.value = '';
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
}
