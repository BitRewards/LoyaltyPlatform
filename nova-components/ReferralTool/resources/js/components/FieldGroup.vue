<template>
    <div>
        <h4 class="text-90 font-normal text-2xl mb-3">{{ name }}</h4>

        <card class="mb-6 py-3 px-6">
            <component
                    v-for="(field, index) in fields"
                    :class="{ 'remove-bottom-border': index == fields.length - 1 }"
                    :key="index"
                    :is="'form-' + field.component"
                    :errors="$parent.validationErrors"
                    :field="field"
            />
        </card>
    </div>
</template>

<script>
    export default {
        props: {
            name: {
                type: String,
                required: true,
            },

            fields: {
                type: Array,
                required: true,
            }
        }
    }
</script>