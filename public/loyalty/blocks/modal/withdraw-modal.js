export default class WithdrawModal {
  constructor() {
    this.el = document.querySelector('.js-withdraw-modal');

    if (!this.el) {
      return
    }

    this.amount = this.el.querySelector('.js-withdraw-amount');
    this.buttons = [...this.el.querySelectorAll('.js-withdraw-button')];
    this.errorClass = 'is-error';
    this.fee = this.el.querySelector('.js-withdraw-fee');

    this.feeType = this.el.querySelector('.js-withdraw-fee-type').value;
    this.feeValue = parseInt(this.el.querySelector('.js-withdraw-fee-value').value);
    this.amountMin = parseInt(this.el.querySelector('.js-withdraw-amount-min').value);
    this.amountMax = parseInt(this.el.querySelector('.js-withdraw-amount-max').value);

    this.amountCharged = this.el.querySelector('.js-withdraw-amount-charged');
    this.total = [...this.el.querySelectorAll('.js-withdraw-total')];
    this.ethereumAddress = document.querySelector('.js-ethereum');
    this.ethInput = this.el.querySelector('.js-withdraw-eth-input');
    this.ethText = this.el.querySelector('.js-withdraw-eth-address');
    this.magicInput = this.el.querySelector('.js-withdraw-magic');
    this.magicText = [...this.el.querySelectorAll('.js-withdraw-magic-details')];
    this.noMagicText = [...this.el.querySelectorAll('.js-withdraw-no-magic-details')];
    this.horizontalSlider = this.el.querySelector('.js-withdraw-modal-slider');
    this.steps = this.el.querySelector('.js-withdraw-modal-steps');
    this.sliderNextClass = 'is-next';
    this.loader = this.el.querySelector('.js-loader');
    this.doneBtn = this.el.querySelector('.js-close-modal');
    this.list = document.querySelector('.js-withdraw-transactions-list');
    this.balances = [...document.querySelectorAll('.js-balance')];

    this.onKeyUp = this.onKeyUp.bind(this);
    this.onButtonClick = this.onButtonClick.bind(this);
    this.calculate = this.calculate.bind(this);
    this.onEthAddressChange = this.onEthAddressChange.bind(this);
    this.onClose = this.onClose.bind(this);
    this.onSuccess = this.onSuccess.bind(this);
    this.onStepsClick = this.onStepsClick.bind(this);
    this.onFinish = this.onFinish.bind(this);
    this.close = this.close.bind(this);

    this.doneBtn.addEventListener('click', this.onFinish);
    this.amount.addEventListener('keyup', this.onKeyUp);
    this.buttons.forEach(item => item.addEventListener('click', this.onButtonClick));
    this.ethereumAddress && this.ethereumAddress.addEventListener('change', this.onEthAddressChange);
    this.steps.addEventListener('click', this.onStepsClick);
    document.body.addEventListener('WITHDRAW_RESET', this.onClose);
    document.body.addEventListener('WITHDRAW_SUCCESS', this.onSuccess);

    this.loader.disabled = true;
    this.onKeyUp();
  }

  onKeyUp() {
    let value = parseInt(this.amount.value);
    let fee = this.calcFee(value);

    if (value && value + fee >= this.amountMin && value + fee <= this.amountMax) {
      this.amount.classList.remove(this.errorClass);
      this.loader.disabled = false;

    } else {
      this.amount.classList.add(this.errorClass);
      this.loader.disabled = true;
    }

    if (value) {
      this.calculate();
    }
  }

  onButtonClick(e) {
    e.preventDefault();

    let target = e.target.closest('.button');
    let amount = parseInt(target.getAttribute('data-amount'));
    this.amount.classList.remove(this.errorClass);
    this.loader.disabled = false;

    if (this.feeType === 'percent') {
      this.amount.value = Math.floor((amount * 100)/ (100 + this.feeValue))
    } else {
      this.amount.value = amount - this.feeValue;
    }

    if (this.amount.value < this.amountMin) {
      // this.amount.value = amount === this.amountMin ? this.amountMin : this.amount.value;
    }

    if (this.amount.value < 0) {
      this.amount.value = 0;
    }

    this.onKeyUp();
  }

  calcFee(value) {
    let fee = 0;
    if (this.feeType === 'percent') {
        fee = Math.ceil(value / 100 * this.feeValue);
    } else {
        fee = this.feeValue;
    }

    return fee
  }

  calculate() {
    let value = parseInt(this.amount.value);

    let fee = this.calcFee(value);
    let charged = Math.ceil(value + fee);

    this.fee.innerHTML = parseFloat(fee).toLocaleString('ru');
    this.amountCharged.innerHTML = parseFloat(charged).toLocaleString('ru');
    this.total.forEach(item => item.innerHTML = parseFloat(value).toLocaleString('ru'));
  }

  onEthAddressChange() {
    this.ethInput.value = this.ethText.innerHTML = this.ethereumAddress.value;
  }

  close() {
    window.loyalty.modalManager.closeAll();
  }

  onClose() {
    this.horizontalSlider.classList.remove(this.sliderNextClass);
    this.steps.classList.remove(this.sliderNextClass);
    this.steps.classList.remove('is-active');
    this.ethInput.value = this.ethText.innerHTML = this.ethereumAddress.value = '';
    this.buttons[0].click();
  }

  onStepsClick(e) {
    e.preventDefault();

    let target = e.target.closest('.steps__link');

    if (target) {
      if (target.getAttribute('data-step') == '1') {
        this.horizontalSlider.classList.remove(this.sliderNextClass);
        this.steps.classList.remove(this.sliderNextClass);
      } else {
        this.horizontalSlider.classList.add(this.sliderNextClass);
        this.steps.classList.add(this.sliderNextClass);
      }
    }
  }

  onFinish() {
    // location.reload()
  }

  onSuccess(e) {
    const data = e.detail.data || {};
    const magic_number = data.magic_number || false;

    this.list.innerHTML = data.withdraw_transactions || '';

      document.dispatchEvent(new Event('transaction-dropdown'));

    this.balances.forEach(item => item.innerHTML = parseInt(data.balance || 0));

    this.magicText.forEach(item => item.classList.remove('withdraw-without-magic-number'));
    this.noMagicText.forEach(item => item.classList.remove('withdraw-without-magic-number'));

    if (!magic_number) {
      this.magicText.forEach(item => item.classList.add('withdraw-without-magic-number'));
    } else {
      this.noMagicText.forEach(item => item.classList.add('withdraw-without-magic-number'));
      this.magicInput.value = magic_number.match(/.{4}/g).join('-')
    }

    this.horizontalSlider.classList.add(this.sliderNextClass);
    this.steps.classList.add(this.sliderNextClass);
    this.steps.classList.add('is-active');
  }
}
