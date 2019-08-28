<template>
    <div>
        <ActionButton v-for="button in field.buttons"
                      :key="field.label"
                      v-bind:label="button.label"
                      v-bind:action="button.action"
                      v-bind:disabled="button.disabled" />
    </div>
</template>

<script>
    export default {
        props: ['resourceName', 'field'],

        mounted() {
            console.log(this)
        }
    }
</script>
