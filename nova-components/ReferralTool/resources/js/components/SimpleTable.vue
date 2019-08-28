<template>
    <loading-view :loading="loading">
        <h2 v-if="card.title" class="mb-6 text-90 font-normal text-2xl">{{ card.title }}</h2>
        <card>
            <table
                    class="table w-full"
                    cellpadding="0"
                    cellspacing="0"
                    data-testid="resource-table"
            >
                <thead>
                <tr>
                    <th v-if="headers.length"
                        v-for="header in headers"
                        class="text-left"
                    >{{ header }}</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="row in data">
                    <td v-for="column in row">{{ column }}</td>
                </tr>
                </tbody>
            </table>
        </card>
    </loading-view>
</template>

<script>
    export default {
        props: {
            card: {
                type: Object,
                required: true
            },

            dataUrl: {
                type: String,
                required: false
            },

            range: {
                type: Number,
                required: false
            }
        },

        watch: {
            range() {
                this.loadData()
            }
        },

        data() {
            return {
                loading: true
            }
        },

        async created() {
            if (!this.card.dataUrl) {
                this.loading = false
            } else {
                this.loadData()
            }
        },

        computed: {
            data() {
                return this.card.data
            },

            headers() {
                return this.card.headers
            }
        },

        methods: {
            async loadData() {
                this.loading = true
                const { data } = await Nova.request().get(this.card.dataUrl + (this.range ? '?range='+this.range : ''))
                this.card.data = Array.isArray(data) ? data : []
                this.loading = false
            }
        },
    }
</script>
