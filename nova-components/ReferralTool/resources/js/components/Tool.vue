<template>
    <div>
        <div class="flex flex-wrap">
            <div class="w-4/5">
                <heading class="mb-6">{{ __('Referral Tool') }}</heading>
            </div>
            <GlobalRangeSelect :ranges="ranges" @global-range-select="selectRange" />
        </div>
        <cards :cards=cards ref="cards"></cards>
    </div>
</template>

<script>
export default {
    data() {
        return {
            cards: [],
            ranges: []
        }
    },

    created: function () {
        let self = this

        fetch('/dashboard/referral-tool/cards')
            .then(r => r.json())
            .then(json => {
                this.cards = json;

                this.cards.forEach(function (card) {
                    if (card.ranges.length) {
                        self.setRanges(card.ranges)
                    }
                })
            });
    },

    methods: {
        selectRange(range) {
            this.$refs.cards.$children.forEach(function (c) {
                if (c.card.ranges.length) {
                    c.$children[0].handleRangeSelected(range)
                }
            })
        },

        setRanges(ranges) {
            this.ranges = ranges
        }
    }
}
</script>
