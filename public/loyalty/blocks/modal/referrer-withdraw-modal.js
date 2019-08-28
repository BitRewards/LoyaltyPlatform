import moment from 'moment'

export default class ReferrerWithdrawModal {
  constructor() {
    this.el = document.querySelector('.js-referrer-withdraw-modal');
    if (!this.el) {
      return
    }

    this.fillInputs = this.fillInputs.bind(this);
    this.reload = this.reload.bind(this);

    document.body.addEventListener('REFERRER_MODAL_BEFORE_OPEN', this.fillInputs)
    document.body.addEventListener('REFERRER_MODAL_CLOSE', this.reload)
  }

  fillInputs(e) {
    const { card, amount, fee, total } = e.detail
    let cardOnlyDigits = card.replace(/[^\d]+/g, '')
    let cardMaskedNumber = "***" + cardOnlyDigits.substring(cardOnlyDigits.length - 4)
    document.querySelector('.js-transaction-open-date').innerHTML = moment().format('DD.MM.YYYY')
    document.querySelector('.js-transaction-open-time').innerHTML = moment().format('HH:mm')
    document.querySelector('.js-transaction-card').innerHTML = cardMaskedNumber
    document.querySelector('.js-transaction-amount').innerHTML = amount
    document.querySelector('.js-transaction-fee').innerHTML = fee
    document.querySelector('.js-transaction-total').innerHTML = total
    document.querySelector('.js-transaction-close-date').innerHTML = moment().add(7, 'days').format('DD.MM.YYYY')
  }

  reload() {
    location.reload()
  }
}
