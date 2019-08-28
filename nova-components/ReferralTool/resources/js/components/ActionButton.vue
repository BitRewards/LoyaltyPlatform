<template>
    <span v-if="!disabled" @click="doAction"
          v-bind:class="{'btn-loading': isLoading}"
          class="btn btn-small btn-default btn-primary">{{ label }}</span>
</template>

<script>
    export default {
        props: ['label', 'action', 'disabled'],

        data() {
            return {
                isLoading: false
            }
        },

        methods: {
            doAction() {
                if (!this.action || this.isLoading) {
                    return
                }

                this.isLoading = true

                fetch(this.action)
                    .then(response => {
                        if(!response.ok) {
                            throw Error('Request failed')
                        }

                        window.location.reload()
                    })
                    .catch(alert)
                    .finally(response => {
                        this.isLoading = false
                    })
            }
        }
    }
</script>
