<template>
    <div>
        <h3 class="text-sm uppercase tracking-wide text-80 bg-30 p-3">{{ filter.name }}</h3>
        <div class="flex flex-wrap p-2">
            <div class="w-2/5">
                <input type="text"
                       :placeholder="filter.translates.from_label"
                       class="w-full form-control form-input form-input-bordered"
                       name="from"
                       :value="value.from"
                       @change="handleChange" />
            </div>
            <div class="w-1/5 text-center p-2">–</div>
            <div class="w-2/5">
                <input type="text"
                       :placeholder="filter.translates.to_label"
                       class="w-full form-control form-input form-input-bordered"
                       name="to"
                       :value="value.to"
                       @change="handleChange" />
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        resourceName: {
            type: String,
            required: true,
        },
        filterKey: {
            type: String,
            required: true,
        },
        lens: String,
    },

    methods: {
        handleChange(event) {
            let value = this.filter.currentValue || {}

            value[event.target.name] = event.target.value

            this.$store.commit(`${this.resourceName}/updateFilterState`, {
                filterClass: this.filterKey,
                value: value,
            })

            this.$emit('change')
        },
    },

    computed: {
        filter() {
            return this.$store.getters[`${this.resourceName}/getFilter`](this.filterKey)
        },

        value() {
            return this.filter.currentValue
        }
    },
}
</script>
