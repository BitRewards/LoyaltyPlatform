import HorizontalSlider from '../horizontal-slider/horizontal-slider'

export default class ConfirmPhoneModal {
  constructor(el) {
    this.el = el;
    this.slider = new HorizontalSlider(this.el.querySelector('.js-confirm-phone-slider'));
    this.bindPhone = this.el.querySelector('.js-bind-phone');
    this.phoneText = [...this.el.querySelectorAll('.js-confirm-phone')];
    this.confirmSMS = this.el.querySelector('.js-confirm-sms');
    this.backButton = this.el.querySelector('.js-modal-back');

    this.onBindPhone = this.onBindPhone.bind(this);
    this.reset = this.reset.bind(this);
    this.onBackClick = this.onBackClick.bind(this);
    this.onSuccess = this.onSuccess.bind(this);

    this.backButton.addEventListener('click', this.onBackClick);
    document.body.addEventListener('CONFIRM_PHONE_BIND', this.onBindPhone);
    document.body.addEventListener('CONFIRM_PHONE_SEND_CODE', this.onSuccess);
    document.body.addEventListener('CONFIRM_PHONE_RESET', this.reset)
  }

  onBackClick(e) {
    e.preventDefault();

    this.slider.slideTo(this.slider.offset / 100 - 1);
    if (this.slider.offset / 100 === 0) {
      this.backButton.classList.remove('is-show');
    }
  }

  onBindPhone(e) {
    this.slider.slideNext();
    this.backButton.classList.add('is-show');
    this.phoneText.forEach(item => item.innerHTML = this.bindPhone.value);
    let hiddenPhone = document.querySelector('.js-hidden-bind-phone');
    hiddenPhone.value = this.bindPhone.value;
  }

  onSuccess() {
    this.slider.slideNext();
    window.loyalty.updateEverything();
  }

  reset() {
    this.bindPhone.value = this.confirmSMS.value = '';
    this.backButton.classList.remove('is-show');
    this.slider.reset();
  }
}
