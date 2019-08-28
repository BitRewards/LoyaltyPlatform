import Util from './../utils'
import Tip from './tip'
import ShareButton from './share-button'

var Invite = {
  created () {
    document.body.addEventListener('INVITE_SHOW', this.showHowItWorksModal)
  },
  components: {
    Tip,
    ShareButton
  },
  methods: {
    showHowItWorksModal () {
      if (!Util.getCookie('referrals_dashboard_how_it_works_seen')) {
        window.loyalty.modalManager.openModal({ detail: { modalName: '.js-how-it-works' } })
      }
    }
  }
}

export default Invite
