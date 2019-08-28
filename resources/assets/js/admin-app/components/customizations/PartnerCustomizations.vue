<template>
    <div class="partner-customizations-component-wrapper well">
        <p v-if="!customizationsWasLoaded">{{ __('Please Wait...') }}</p>
        <template v-else>
            <p>{{ __("Leave any field blank if you don't want to change default value.") }}</p>
            <p>{{ __('Otherwise provide required value. You can insert special placeholders into your texts (they are listed in each setting).') }}</p>

            <div v-for="customization in customizations" class="form-group">
                <label>{{ customization.name }}</label>

                <template v-if="isTypeOf(customization, 'text')">
                    <input type="text" class="form-control customization-field" :value="customization.value" :data-field="customization.setting" @keyup="updatePreview($event, customization)">
                    <span class="help-block" :id="customization.setting"></span>
                </template>

                <select v-if="isTypeOf(customization, 'select')" class="form-control customization-field" :data-field="customization.setting">
                    <option :value="val" v-for="label,val in customization.options" :selected="val == customization.value">
                        {{ label }}
                    </option>
                </select>

                <div class="well well-sm">
                    {{ __('Default Value:') }} <code>{{ defaultCustomizationValue(customization) }}</code>
                    <div v-if="hasPlaceholders(customization)" class="customization-placeholders-list">
                        <p>{{ __('Possible Placeholders:') }}</p>
                        <ul>
                            <li v-for="title,placeholder in customization.placeholders">
                                <b>{{ '{' + placeholder + '}' }}</b> &mdash; {{ title }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
export default {
    props: ['partner-id'],

    mounted() {
        this.bindCrudFormEvent()
        this.loadCustomizations()
    },

    methods: {
        bindCrudFormEvent() {
            window.jQuery('.crud-entry-update-form').on('submit', (e) => {
                e.preventDefault()

                this.shouldSaveCustomizations(() => {
                    e.target.submit()
                })
            })
        },

        loadCustomizations() {
            if (this.customizationsAreLoading || this.customizationsWasLoaded) {
                return
            }

            this.customizationsAreLoading = true
            this.customizationsWasLoaded = false

            this.$http
                .get(`/admin/partner/${this.partnerId}/customizations`)
                .then(response => {
                    this.customizations = (typeof response.data === 'string' ? JSON.parse(response.data) : response.data).data
                    this.fillCustomValues()
                    this.customizationsAreLoading = false
                    this.customizationsWasLoaded = true
                })
                .catch(error => {
                    this.customizationsAreLoading = false
                    this.customizationsWasLoaded = true
                })
        },

        fillCustomValues() {
            this.customValues = {}

            this.customizations.forEach(customization => {
                this.customValues[customization.setting] = customization.value
            })
        },

        shouldSaveCustomizations(callback) {
            callback = typeof callback === 'function' ? callback : () => {}

            this.$http
                .put(`/admin/partner/${this.partnerId}/customizations`, this.customizationsForm(), {
                    headers: { 'content-type': 'application/json' },
                })
                .then(response => {
                    callback()
                })
                .catch(error => {
                    alert(error)
                })
        },

        customizationsForm() {
            let form = {}

            window.jQuery('.customization-field').each((idx, el) => {
                el = $(el)

                form[el.data('field')] = el.val()
            })

            return form
        },

        hasPlaceholders(customization) {
            return customization.placeholders && Object.keys(customization.placeholders).length > 0
        },

        isTypeOf(customization, type) {
            return this.getType(customization) === type
        },

        getType(customization) {
            if (typeof customization.type === 'undefined') {
                return 'text'
            }

            return customization.type
        },

        defaultCustomizationValue(customization) {
            switch (this.getType(customization)) {
                case 'text':
                    return customization.default ? customization.default : __('not specified')
                case 'select':
                    const index = Object.keys(customization.options).find((val, index) => index == customization.value) || 0
                    return customization.options[index]
            }
        },

        updatePreview(e, customization) {
            if (typeof customization.preview_replacements === 'undefined') {
                return
            }

            // Here we will clear any previous update timeout for given customization and
            // then create new timeout that will be executed in 500ms. This will make
            // us sure that we're not DDoSing user's browser with constant updates.

            if (typeof this.previewTimeouts[customization.setting] !== 'undefined' && this.previewTimeouts[customization.setting]) {
                clearTimeout(this.previewTimeouts[customization.setting]);
            }

            this.previewTimeouts[customization.setting] = setTimeout(() => {
                let value = e.srcElement.value

                for (let placeholder in customization.preview_replacements) {
                    value = value.replace(`{${placeholder}}`, customization.preview_replacements[placeholder])
                }

                window.jQuery(`#${customization.setting}`).text(value)
            }, 500)
        }
    },

    data() {
        return {
            customizations: [],
            customValues: {},
            previewTimeouts: {},
            customizationsAreLoading: false,
            customizationsWasLoaded: false,
        }
    }
}
</script>
