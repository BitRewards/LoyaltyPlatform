import Datepicker from 'vuejs-datepicker'
import axios from 'axios'
import { en, ru } from 'vuejs-datepicker/dist/locale'
import moment from 'moment'
import Tip from './tip'
import HowItWorkLink from './how-it-work-link'

var Dashboard = {
  data() {
    return {
      isDropdownShow: false,
      activeItem: window.i18n.REFERRAL_STATISTIC_PERIOD_LAST_7_DAYS,
      activeItemIndex: 0,
      showDatePicker: false,
      en: en,
      ru: ru,
      startDate: null,
      endDate: null,
      isLoaded: false,
      statistic: {
      },
      filters: [
        //{ title: window.i18n.REFERRAL_STATISTIC_PERIOD_TODAY, type: 'Today' },
        // { title: window.i18n.REFERRAL_STATISTIC_PERIOD_YESTERDAY, type: 'Yesterday' },
        // { title: window.i18n.REFERRAL_STATISTIC_PERIOD_THIS_WEEK, type: 'This week' },
        // { title: window.i18n.REFERRAL_STATISTIC_PERIOD_LAST_WEEK, type: 'Last week' },
        // { title: window.i18n.REFERRAL_STATISTIC_PERIOD_THIS_MONTH, type: 'This month' },
        // { title: window.i18n.REFERRAL_STATISTIC_PERIOD_LAST_MONTH, type: 'Last month' },
        // { title: window.i18n.REFERRAL_STATISTIC_PERIOD_THIS_YEAR, type: 'This year' },

        { title: window.i18n.REFERRAL_STATISTIC_PERIOD_LAST_7_DAYS, type: 'Last 7 days' },
        { title: window.i18n.REFERRAL_STATISTIC_PERIOD_LAST_30_DAYS, type: 'Last 30 days' },
        { title: window.i18n.REFERRAL_STATISTIC_PERIOD_ALL_PERIOD, type: 'All period' },
        // { title: window.i18n.REFERRAL_STATISTIC_PERIOD_LAST_60_DAYS, type: 'Last 60 days' },
        { title: window.i18n.REFERRAL_STATISTIC_PERIOD, type: 'Period' }
      ]
    }
  },
  created () {
    document.body.addEventListener('DASHBOARD_SHOW', () => this.getLastDaysStatistic(7))
  },
  components: {
    Datepicker,
    Tip,
    HowItWorkLink
  },
  methods: {
    getStatistic (from, to) {
      this.isLoaded = false

      axios(`${window.URLS.REFERRAL_STATISTIC_URL}?from=${from}&to=${to}`)
      .then(response => response.data.data)
      .then(data => {
        this.statistic = data
        this.isLoaded = true
      })
    },

    getTodayStatistic () {
      let from = moment().format('YYYY-MM-DD')
      let to = moment().add(1, 'days').format('YYYY-MM-DD')
      this.getStatistic(from, to)
    },

    getYesterdayStatistic () {
      let from = moment().subtract(1, 'days').format('YYYY-MM-DD')
      let to = moment().format('YYYY-MM-DD')
      this.getStatistic(from, to)
    },

    getThisWeekStatistic () {
      let from = moment().startOf('isoWeek').format('YYYY-MM-DD')
      let to = moment().endOf('isoWeek').format('YYYY-MM-DD')
      this.getStatistic(from, to)
    },

    getLastWeekStatistic () {
      let from = moment().startOf('isoWeek').subtract(7, 'days').format('YYYY-MM-DD')
      let to = moment().startOf('isoWeek').subtract(1, 'days').format('YYYY-MM-DD')
      this.getStatistic(from, to)
    },

    getThisMonthStatistic () {
      let from = moment().startOf('month').format('YYYY-MM-DD')
      let to = moment().endOf('month').format('YYYY-MM-DD')
      this.getStatistic(from, to)
    },

    getLastMonthStatistic () {
      let from = moment().startOf('month').subtract(1, 'month').format('YYYY-MM-DD')
      let to = moment().endOf('month').subtract(1, 'month').format('YYYY-MM-DD')
      this.getStatistic(from, to)
    },

    getThisYearStatistic () {
      let from = moment().startOf('year').format('YYYY-MM-DD')
      let to = moment().format('YYYY-MM-DD')
      this.getStatistic(from, to)
    },

    getAllPeriodStatistic () {
      let from = '2018-09-01'
      let to = moment().format('YYYY-MM-DD')
      this.getStatistic(from, to)
    },

    getLastDaysStatistic (days) {
      let from = moment().subtract(days, 'days').format('YYYY-MM-DD')
      let to = moment().format('YYYY-MM-DD')
      this.getStatistic(from, to)
    },

    onStartDateSelected(date) {
      this.startDate = date
      if (this.startDate && this.endDate) {
        this.getStatistic(moment(this.startDate).format('YYYY-MM-DD'), moment(this.endDate).format('YYYY-MM-DD'))
      }
    },

    onEndDateSelected(date) {
      this.endDate = date
      if (this.startDate && this.endDate) {
        this.getStatistic(moment(this.startDate).format('YYYY-MM-DD'), moment(this.endDate).format('YYYY-MM-DD'))
      }
    },

    toggleDashboard () {
      this.isDropdownShow = !this.isDropdownShow
    },

    customFormatter (date) {
      return window.LANGUAGE === 'en' ? moment(date).format('MM-DD-YYYY') : moment(date).format('DD.MM.YYYY');
    },

    onFilterSelect (item, index) {
      this.activeItem = item.title
      this.activeItemIndex = index
      this.isDropdownShow = false

      switch (item.type) {
        case 'Today':
          this.showDatePicker = false
          this.getTodayStatistic()
          break

        case 'Yesterday':
          this.showDatePicker = false
          this.getYesterdayStatistic()
          break

        case 'This week':
          this.showDatePicker = false
          this.getThisWeekStatistic()
          break

        case 'Last week':
          this.showDatePicker = false
          this.getLastWeekStatistic()
          break

        case 'This month':
          this.showDatePicker = false
          this.getThisMonthStatistic()
          break

        case 'Last month':
          this.showDatePicker = false
          this.getLastMonthStatistic()
          break

        case 'This year':
          this.showDatePicker = false
          this.getThisYearStatistic()
          break

        case 'All period':
          this.showDatePicker = false
          this.getAllPeriodStatistic()
          break

        case 'Last 7 days':
          this.showDatePicker = false
          this.getLastDaysStatistic(7)
          break

        case 'Last 30 days':
          this.showDatePicker = false
          this.getLastDaysStatistic(30)
          break

        case 'Last 60 days':
          this.showDatePicker = false
          this.getLastDaysStatistic(60)
          break

        case 'Period':
          this.showDatePicker = true
          break
      }
    },

    trackHoverEvent () {
      console.log('df')
      alert('hover!')
    }
  }
}

export default Dashboard
