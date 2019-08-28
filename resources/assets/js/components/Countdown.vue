<template>
    <div class="centered">
        <template v-if="secondsLeftLive > 0">
            <div class="block">
                <p class="digit">{{ days | two_digits }}</p>
                <p class="text">{{ __("days") }}</p>
            </div>
            <div class="block">
                <p class="digit">{{ hours | two_digits }}</p>
                <p class="text">{{ __("hours") }}</p>
            </div>
            <div class="block">
                <p class="digit">{{ minutes | two_digits }}</p>
                <p class="text">{{ __("minutes") }}</p>
            </div>
            <div class="block">
                <p class="digit">{{ seconds | two_digits }}</p>
                <p class="text">{{ __("seconds") }}</p>
            </div>
        </template>
        <template v-else>
            <div class="expired">
                {{finishedText}}
            </div>
        </template>
    </div>
</template>
<script>
    export default {
        created() {
            this.interval = setInterval(() => {
                this.secondsLeftLive--;
            },1000);
        },
        destroyed() {
            clearInterval(this.interval);
        },

        props : [
            'secondsLeft',
            'finishedText'
        ],
        data() {
            return {
                interval: null,
                secondsLeftLive: this.secondsLeft,
            }
        },
        computed: {
            seconds() {
                return (this.secondsLeftLive) % 60;
            },
            minutes() {
                return Math.trunc((this.secondsLeftLive) / 60) % 60;
            },
            hours() {
                return Math.trunc((this.secondsLeftLive) / 60 / 60) % 24;
            },
            days() {
                return Math.trunc((this.secondsLeftLive) / 60 / 60 / 24);
            }
        }
    }
</script>
<style scoped>
    .block {
        display: inline-block;
        margin: 5px;
    }
    .text {
        color: #999;
        font-size: 14px;
        margin-top:5px;
        margin-bottom: 5px;
        text-align: center;
    }
    .digit {
        color: #77BFE3;
        font-size: 30px;
        font-weight: 100;
        margin: 5px;
        text-align: center;
    }

    .expired {
        text-align: center;
        font-size: 30px;
        color: #ff9805;
        margin: 10px;
    }
</style>