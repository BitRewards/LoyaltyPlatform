import HorizontalSlider from '../horizontal-slider/horizontal-slider'

export default class ChangePasswordModal {
  constructor(el) {
    this.el = el;
    this.slider = new HorizontalSlider(this.el.querySelector('.js-change-password-modal-slider'));
    this.reset = this.reset.bind(this);
    this.forgotScene = this.el.querySelector('.js-forgot-password');
    this.backButton = this.el.querySelector('.js-modal-back');
    this.forgotButton = this.el.querySelector('.js-forgot-password-button');

    this.onBackClick = this.onBackClick.bind(this);
    this.onForgotClick = this.onForgotClick.bind(this);
    this.onResetPasswordSuccess = this.onResetPasswordSuccess.bind(this);

    this.backButton.addEventListener('click', this.onBackClick);
    this.forgotButton.addEventListener('click', this.onForgotClick);

    document.body.addEventListener('CHANGE_PASSWORD_OLD', this.slider.slideNext);
    document.body.addEventListener('CHANGE_PASSWORD_NEW', this.slider.slideNext);
    document.body.addEventListener('CHANGE_PASSWORD_RESET', this.reset);
    document.body.addEventListener('RESET_PASSWORD_SUCCESS', this.onResetPasswordSuccess);
  }

  onBackClick(e) {
    e.preventDefault();

    this.forgotScene.classList.remove('is-show');
  }

  onForgotClick(e) {
    e.preventDefault();

    this.forgotScene.classList.add('is-show');
  }

  onResetPasswordSuccess() {
    this.forgotScene.classList.remove('is-show');
    this.slider.slideTo(2);
  }

  reset() {
    this.slider.reset();
  }
}
