import HowItWorkLink from './how-it-work-link'

var Tab = {
  data() {
    return {
      activeIndex: 0
    }
  },
  components: {
    HowItWorkLink
  },
  methods: {
    onClick(index) {
      this.activeIndex = index
    }
  }
}

export default Tab
