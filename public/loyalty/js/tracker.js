class Tracker {
  constructor() {
    document.body.addEventListener('click', this.onClick.bind(this))
  }

  trackGaEvent (category, action, label = window.PARTNER_NAME) {
    if (window.gtag && window.IS_REFERRAL_PROGRAM && category && action) {
      console.log('Track GA event category: ' + category + ' action: ' + action);
      window.gtag('event', action, {
        'event_category': category,
        'event_label': label
      });
    }
  }

  onClick (e) {
    const target = e.target.closest('.js-track-button')

    if (!target) return

    const category = target.getAttribute('data-event-category')
    const action = target.getAttribute('data-event-action')

    this.trackGaEvent(category, action)
  }
}

let tracker = new Tracker()
export default tracker
