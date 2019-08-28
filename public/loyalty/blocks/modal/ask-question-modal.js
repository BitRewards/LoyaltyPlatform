'use strict';

export default class AskQuestionModal {
  constructor(el) {
    this.el = el;
    this.slider = this.el.querySelector('.js-ask-question-modal-steps');
    this.sendEmail = this.el.querySelector('.js-send-email');


    document.body.addEventListener('SEND_QUESTION_SUCCESS', this.showSuccessScreen.bind(this));
  }

  showSuccessScreen(e) {
    this.slider.style.transform = 'translateX(-100%)';
    this.sendEmail.innerHTML = e.detail.data.email;

    setTimeout(() => {
      window.loyalty.modalManager.closeAll();
      setTimeout(() => {
        this.reset();
      }, 1000);
    }, 3000);
  }

  reset() {
    $(this.el).find('[name=message]').val("");
    this.slider.style.transform = '';
  }

}
