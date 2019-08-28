<template>
    <input
            :id="field.attribute"
            :dusk="field.attribute"
            v-model="value"
            v-bind="extraAttributes"
            type="hidden"
            disabled
    />
</template>

<script>
    import { FormField, HandlesValidationErrors } from 'laravel-nova'

    export default {
        mixins: [HandlesValidationErrors, FormField],

        computed: {
            defaultAttributes() {
                return {
                    type: this.field.type || 'text',
                    min: this.field.min,
                    max: this.field.max,
                    step: this.field.step,
                    pattern: this.field.pattern,
                    placeholder: this.field.placeholder || this.field.name,
                    class: this.errorClasses,
                }
            },

            extraAttributes() {
                const attrs = this.field.extraAttributes

                return {
                    ...this.defaultAttributes,
                    ...attrs,
                }
            },
        },
    }
</script>
