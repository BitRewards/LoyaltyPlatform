import Util from './../utils'
import Tip from './tip'

export default {
  created () {
    document.body.addEventListener('EARN_SHOW', this.showHowItWorksModal)
  },
  components: {
    Tip
  },
  methods: {
    showHowItWorksModal () {
      if (!Util.getCookie('loyalty_earn_how_it_works_seen')) {
        window.loyalty.modalManager.openModal({ detail: { modalName: '.js-how-it-works' } })
      }
    }
  }
}
