<template>
    <div>
        <div class="flex flex-wrap">
            <div class="w-4/5">
                <heading class="mb-6">{{ __('Tool statistic') }}</heading>
            </div>
            <GlobalRangeSelect :ranges="ranges" @global-range-select="selectRange" />
        </div>

        <div v-if="shouldShowCards">
            <cards v-if="smallCards.length > 0" :cards="smallCards" ref="cards" class="mb-3" />
            <cards v-if="largeCards.length > 0" :cards="largeCards" ref="largeCards" size="large" />
        </div>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                ranges: [],
                cards: [],
            }
        },

        created() {
            this.fetchCards()
        },

        methods: {
            async fetchCards() {
                const { data: cards } = await Nova.request().get('/dashboard/tools-statistic/cards')
                this.cards = cards

                cards.forEach((card) => {
                    if (card.ranges && card.ranges.length) {
                        this.ranges = card.ranges
                    }
                })
            },

            selectRange(range) {
                this.$refs.cards.$children.forEach(function (c) {
                    if (c.card.ranges.length) {
                        c.$children[0].handleRangeSelected(range)
                    }
                })

                this.$refs.largeCards.$children.forEach(function (c) {
                    //@todo fix hardcoded range for simple table
                    c.$children[0].range = range
                })
            }
        },

        computed: {
            shouldShowCards() {
                return this.cards.length > 0
            },

            smallCards() {
                return _.filter(this.cards, c => c.width !== 'full')
            },

            largeCards() {
                return _.filter(this.cards, c => c.width == 'full')
            },
        },
    }
</script>