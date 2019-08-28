import Vue from 'vue'
import axios from 'axios'
import Tab from './tab'
import Tip from './tip'

var ReferrerBalance = {
  name: 'referrer-balance',
  data () {
    return {
      isWithdrawShow: false,
      amount: '',
      card: '',
      firstname: '',
      secondname: '',
      isLoading: false,
      formError: null,
      feeType: window.partnerSettings.fiatWithdrawFeeType,
      feeAmount: window.partnerSettings.fiatWithdrawFeeAmount
    }
  },
  components: {
    Tab,
    Tip
  },
  computed: {
    fee () {
      if (!this.amount) return ''
      switch (this.feeType) {
        case 'percent':
          return this.amount * this.feeAmount / 100

        case 'fixed':
          return this.feeAmount
      }
    },
    total () {
      if (!this.amount) return ''
      return this.amount - this.fee
    }
  },
  methods: {
    showWithdraw () {
      this.isWithdrawShow = true
    },
    validateBeforeSubmit () {
      this.$validator.validateAll().then(success => {
        if (success) {
          this.send()
        }
      })
    },
    send () {
      this.isLoading = true

      axios({
        url: window.URLS.FIAT_WITHDRAW,
        method: 'post',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: JSON.stringify({
            cardNumber: this.card,
            firstName: this.firstname,
            secondName: this.secondname,
            withdrawAmount: this.amount
        })
      })
      .then(response => {
        this.formError = null
        this.isLoading = false
        this.isWithdrawShow = false
        this.resetForm()
        window.loyalty.modalManager.openModal({ detail: {
          modalName: '.js-referrer-withdraw-modal',
          card: this.card,
          amount: this.amount,
          fee: this.fee,
          total: this.total
        } })
      })
      .catch (error => {
        this.isLoading = false
        if (error.response) {
          let { data } = error.response.data
          this.formError = ''
          Object.keys(data).forEach(key => {
            this.formError += data[key] + '\n'
          })
        } else {
          this.formError = error.message
        }
      })
    },
    resetForm () {
      this.amount = this.card = this.firstname = this.secondname = ''
      this.formError = null
      this.$nextTick(() => this.$validator.reset())
    }
  }
}

export default ReferrerBalance
