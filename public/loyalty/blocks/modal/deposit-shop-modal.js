export default class DepositShopModal {
  constructor() {
    this.el = document.querySelector('.js-deposit-shop-modal');
      if (!this.el) {
          return
      }
    this.steps = this.el.querySelector('.js-deposit-shop-steps');
    this.slider = this.el.querySelector('.js-deposit-shop-slider');
    this.sliderNextClass = 'is-next';
    this.goNextButton = this.el.querySelector('.js-deposit-shop-next');
    this.list = document.querySelector('.js-deposit-transactions-list');
    this.balances = [...document.querySelectorAll('.js-balance')];

    this.slideNext = this.slideNext.bind(this);
    this.onStepsClick = this.onStepsClick.bind(this);
    this.reset = this.reset.bind(this);
    this.close = this.close.bind(this);

    this.steps.addEventListener('click', this.onStepsClick);
    this.goNextButton.addEventListener('click', this.slideNext);
    document.body.addEventListener('DEPOSIT_SHOP_RESET', this.reset);
    document.body.addEventListener('DEPOSIT_SHOP_SUCCESS', this.close)
  }

  slideNext() {
    this.slider.classList.add(this.sliderNextClass);
    this.steps.classList.add(this.sliderNextClass);
  }

  reset() {
    this.slider.classList.remove(this.sliderNextClass);
    this.steps.classList.remove(this.sliderNextClass);
  }

  close(e) {
    const data = e.detail.data || {};

    this.list.innerHTML = data.transactions || '';

    this.balances.forEach(item => item.innerHTML = parseInt(data.balance || 0));
    window.loyalty.modalManager.closeAll();

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
