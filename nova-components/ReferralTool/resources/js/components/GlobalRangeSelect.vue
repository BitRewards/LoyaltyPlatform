<template>
    <div class="w-1/5 text-right">
        <select v-if="ranges.length" @change="$emit('global-range-select', $event.target.value)" :value="value">
            <option v-for="range in ranges" :value="range.value">{{ range.label }}</option>
        </select>
    </div>
</template>

<script>
    export default {
        props: {
            ranges: {
                type: Array,
                default: []
            }
        },

        computed: {
            value() {
                return this.ranges[0].value
            }
        }
    }
</script>