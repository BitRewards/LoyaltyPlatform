import Util from 'utils'
import tracker from 'tracker'
import moment from 'moment'

export default class HowItWorksModal {
  constructor() {
    this.el = document.querySelector('.js-how-it-works');
    if (!this.el) {
        return
    }

    this.understandButton = this.el.querySelector('.js-understand-button');
    this.closeButton = this.el.querySelector('.js-close-button');
    
    this.onClose = this.onClose.bind(this);

    document.body.addEventListener('HOW_IT_WORKS_MODAL_CLOSE', this.onClose);
    document.body.addEventListener('HOW_IT_WORK_MODAL_OPEN', this.onOpen);

    this.understandButton.addEventListener('click', this.onClose);
    this.closeButton.addEventListener('click', this.onClose);
  }

  onOpen() {
    if (this.isFirstTimeOpen) {
      tracker.trackGaEvent('Onboarding', 'onboardingInstructionShowFirstTime')
    } else {
      tracker.trackGaEvent('Onboarding', 'onboardingInstructionShow')
    }
  }

  onClose() {
    if (this.isFirstTimeOpen()) {
      if (window.IS_REFERRAL_PROGRAM) {
        this._setSeenCookie('referrals_dashboard_how_it_works_seen')

        tracker.trackGaEvent('Onboarding', 'onboardingInstructionUnderstandClick');
        tracker.trackGaEvent('Onboarding', 'onboardingInstructionCloseClick')
      }
        this._setSeenCookie('loyalty_earn_how_it_works_seen')
    }
  }

  _setSeenCookie(name) {
    Util.setCookie(name, '1', moment().add(10, 'year').toDate());
  }

  isFirstTimeOpen() {
    if (window.IS_REFERRAL_PROGRAM) {
      return !Util.getCookie('referrals_dashboard_how_it_works_seen')
    }

    return !Util.getCookie('loyalty_earn_how_it_works_seen')
  }
}
