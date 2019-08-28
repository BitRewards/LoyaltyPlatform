<template>
    <loading-view :loading="loading">
        <form v-if="fields" @submit.prevent="submit" autocomplete="off">
            <FieldGroup v-for="group in groups"
                        :name="group"
                        :key="group"
                        :fields="groupFields(group)"
                        :errors="validationErrors"/>
            <!-- Create Button -->
            <div class="bg-30 flex px-8 py-4">
                <progress-button
                        class="ml-auto mr-3"
                        @click.native="submit"
                        :disabled="isWorking"
                        :processing="isWorking"
                >
                    {{ __('Save') }}
                </progress-button>
            </div>
        </form>
    </loading-view>
</template>

<script>
    import {Errors} from 'laravel-nova';

    export default {
        props: {
            url: {
                type: String,
                required: true
            }
        },

        data() {
            return {
                loading: true,
                fields: [],
                groups: [],
                validationErrors: new Errors(),
                isWorking: false
            }
        },

        async created() {
            this.loadForm()
        },

        methods: {
            async loadForm() {
                this.fields = []
                this.groups = []

                const { data: fields } = await Nova.request().get(this.url)

                let groups = []

                fields.forEach(field => {
                    if (field.group && groups.indexOf(field.group) === -1) {
                        groups.push(field.group)
                    }
                })

                this.fields = fields
                this.groups = groups
                this.loading = false
            },

            groupFields(group) {
                return this.fields.filter(function (field) {
                    if (arguments.length) {
                        return field.group === group
                    }

                    return !field.group
                })
            },

            async submit() {
                this.isWorking = true

                try {
                    const response = await this.submitRequest()

                    this.$toasted.show(this.__('Settings successfully updated'), {
                        position: 'top-center'
                    })

                    this.loadForm()
                } catch (error) {
                    if (error.response.status == 422) {
                        this.validationErrors = new Errors(error.response.data.errors)
                    }

                    this.$toasted.error(this.__('Settings update error!'), {
                        position: 'top-center'
                    })
                }

                this.isWorking = false
            },

            async submitRequest() {
                return Nova.request().post(this.url, this.createResourceFormData())
            },

            createResourceFormData() {
                return _.tap(new FormData(), formData => {
                    _.each(this.fields, field => {
                        field.fill(formData)
                    })
                })
            },
        },
    }
</script>
