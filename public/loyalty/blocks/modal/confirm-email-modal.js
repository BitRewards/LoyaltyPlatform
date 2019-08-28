import HorizontalSlider from '../horizontal-slider/horizontal-slider'

export default class ConfirmEmailModal {
  constructor(el) {
    this.el = el;
    this.slider = new HorizontalSlider(this.el.querySelector('.js-confirm-email-slider'));
    this.bindEmail = this.el.querySelector('.js-bind-email');
    this.emailText = [...this.el.querySelectorAll('.js-confirm-email')];
    this.confirmCode = this.el.querySelector('.js-confirm-code');
    this.backButton = this.el.querySelector('.js-modal-back');

    this.onClick = this.onClick.bind(this);
    this.onBindEmail = this.onBindEmail.bind(this);
    this.onSendCode = this.onSendCode.bind(this);
    this.reset = this.reset.bind(this);
    this.onBackClick = this.onBackClick.bind(this);
    this.onSuccess = this.onSuccess.bind(this);

    this.el.addEventListener('click', this.onClick);
    this.backButton.addEventListener('click', this.onBackClick);
    document.body.addEventListener('CONFIRM_EMAIL_BIND', this.onBindEmail);
    document.body.addEventListener('CONFIRM_EMAIL_SEND_BIND', this.slider.slideNext);
    document.body.addEventListener('CONFIRM_EMAIL_SEND_CODE', this.onSendCode);
    document.body.addEventListener('CONFIRM_EMAIL_UPDATE', this.onSuccess);
    document.body.addEventListener('CONFIRM_EMAIL_RESET', this.reset)
  }

  onClick(e) {
    let target = e.target.closest('.js-go-back');

    if (target) {
      this.slider.slidePrev();
      return;
    }
  }

  onBackClick(e) {
    e.preventDefault();

    this.slider.slideTo(this.slider.offset / 100 - 1);
    if (this.slider.offset / 100 === 0) {
      this.backButton.classList.remove('is-show');
    }
  }

  onBindEmail(e) {
    // const data = e.detail.data || {};
    // data.needBind ? this.slider.classList.add('is-full') //if need bind accounts
    this.slider.slideNext();
    this.backButton.classList.add('is-show');
    this.emailText.forEach(item => item.innerHTML = this.bindEmail.value)
    let hiddenEmail = document.querySelectorAll('.js-hidden-bind-email');
    hiddenEmail.forEach(item => item.value = this.bindEmail.value);
  }

  onSendCode(e) {
    // const data = e.detail.data || {};
    this.slider.slideNext();
    window.loyalty.updateEverything();
  }

  onSuccess(e) {
    this.slider.slideNext();
    window.loyalty.updateEverything();
  }

  reset() {
    this.bindEmail.value = this.confirmCode.value = '';
    this.slider.reset();
    this.backButton.classList.remove('is-show');
  }
}
