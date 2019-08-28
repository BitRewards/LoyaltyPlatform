Nova.booting((Vue, router, store) => {
    router.addRoutes([
        {
            name: 'referral-tool',
            path: '/referral-tool',
            component: require('./components/Tool')
        },
        {
            name: 'settings',
            path: '/settings',
            component: require('./components/Settings')
        },
        {
            name: 'tools-statistic',
            path: '/tools-statistic',
            component: require('./components/ToolsStatistic')
        }
    ])
})

Nova.booting((Vue, router, store) => {
    Vue.component('index-button-group', require('./components/ButtonGroup'))
    Vue.component('form-hidden-field', require('./components/HiddenField'))
    Vue.component('extended-value-metric', require('./components/ExtendedValueMetric'))


    Vue.component('StaticValue', require('./components/StaticValue'))
    Vue.component('ActionButton', require('./components/ActionButton'))
    Vue.component('ValueRange', require('./components/ValueRange'))
    Vue.component('FieldGroup', require('./components/FieldGroup'))
    Vue.component('CustomForm', require('./components/CustomForm'))
    Vue.component('Settings', require('./components/Settings'))
    Vue.component('SimpleTable', require('./components/SimpleTable'))
    Vue.component('ToolStatistic', require('./components/ToolsStatistic'))
    Vue.component('GlobalRangeSelect', require('./components/GlobalRangeSelect'))
})
