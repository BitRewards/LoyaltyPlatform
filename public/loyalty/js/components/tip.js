import tracker from '../tracker'

export default {
  name: 'tip',
  template: `
    <div class="tip c-incut" v-if="isShow" @mouseover.once="onTipHover">
      <p class="tip__text">
        <slot name="text"></slot>
      </p>
      <slot name="action"></slot>
      <button type="button" class="tip__close" :aria-label="aria" @click.prevent="hide">
        <svg class="tip__close-icon" aria-hidden="true">
          <use xlink:href="#popup-close"></use>
        </svg>
      </button>
    </div>
  `,
  props: {
    aria: String,
    category: String,
    hover: String,
    close: String
  },
  data () {
    return {
      isShow: true
    }
  },
  methods: {
    hide () {
      this.isShow = false

      tracker.trackGaEvent(this.category, this.close)
    },
    onTipHover () {
      tracker.trackGaEvent(this.category, this.hover)
    }
  }
}
