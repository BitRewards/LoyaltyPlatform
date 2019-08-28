Nova.booting((Vue, router, store) => {
    Vue.component('index-differentiated-referral-cashback', require('./components/IndexField'))
    Vue.component('detail-differentiated-referral-cashback', require('./components/DetailField'))
    Vue.component('form-differentiated-referral-cashback', require('./components/FormField'))
})
