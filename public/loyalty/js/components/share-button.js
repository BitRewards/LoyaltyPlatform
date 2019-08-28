import axios from 'axios'
import tracker from '../tracker'

export default {
  name: 'share-button',
  template: `
    <button type="button"
      class="button button_viewtype_share c-primary-clipboard"
      :class="isCopied ? 'is-hide' : ''"
      :data-hover-text="hoverText"
      :data-success-text="successText"
      @click="copyToClipboard"
    >
      <span class="button__text">
        {{ clipboardText }}
      </span>
      <svg class="button__icon c-primary-fill">
        <use xlink:href="#clipboard"></use>
      </svg>
    </button>`,

  props: {
    hoverText: String,
    successText: String,
    clipboardText: String,
    eventCategory: String,
    eventAction: String,
    noCutLink: Boolean
  },
  async created () {
    if (this.clipboardText && !this.noCutLink) {
      const { data } =  await axios.post('https://bit.rw/save', { url: this.clipboardText })
      this.clipboardText = data.url
    }
  },
  data () {
    return {
      isCopied: false
    }
  },
  methods: {
    copyToClipboard () {
      this.$copyText(this.clipboardText).then(() => {
        this.isCopied = true

        tracker.trackGaEvent(this.eventCategory, this.eventAction)

        setTimeout(() => {
          this.isCopied = false
        }, 5000);
      })
    }
  }
}
