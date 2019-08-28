import tracker from '../tracker.js'

var HowItWorkLink = {
  name: 'how-it-work-link',
  template: '<a class="link link_viewtype_standard c-primary-color" @click.prevent="showModal"><slot></slot></a>',
  props: {
    category: String,
    clickEvent: String
  },
  methods: {
    showModal () {
      window.loyalty.tabManager.openTabByID('invite');
      window.loyalty.modalManager.openModal({ detail: { modalName: '.js-how-it-works' } })

      tracker.trackGaEvent(this.category, this.clickEvent)
    }
  }
}

export default HowItWorkLink
