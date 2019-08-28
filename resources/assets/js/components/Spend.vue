<template>
    <div>
        <h4>
            {{ __("Redeem points") }}
            <small>/ {{ __("balance") }} {{currentClient.balance}}</small>
        </h4>

        <p>{{ __("Enter the current order total for the system to calculate and suggest an optimal discount") }}:</p>
        <input type="number" class="form-control input-lg" @keypress.enter="getAvailableRewards" @keypress="clearRewards" @change="clearRewards" v-model="orderTotal" :placeholder="__('Order total')">

        <template v-if="!(rewards && rewards.length)">
            <br>
            <button class="btn btn-primary" @click="getAvailableRewards"
                    v-bind:class="{ 'is-load': $root.state.loading }"
                    v-bind:disabled="$root.state.loading > 0">
                <i class="fa fa-calculator"></i> {{ __("Show available rewards") }}
            </button>
            <br>
        </template>

        <template v-if="rewards">
            <br>
            <template v-if="rewards.length">
            <table class="table " style="margin-bottom: 0;">
                <template v-for="(reward, i) in rewards">
                    <tr v-if="i == 0 || displayAll">
                        <td colspan="10" class="">
                            <button class="btn btn-primary " @click="acquire(reward)" ><span v-html="__('Take a discount %s for %s points', reward.discountStr, reward.price)"></span> &rarr;</button>
                        </td>
                    </tr>
                    <tr v-if="i == 0 || displayAll">
                        <td colspan="10" class="small grey ">
                            <div v-html="reward.title"></div>
                        </td>
                    </tr>
                    <tr v-if="i == 0 || displayAll">
                        <td colspan="10">&nbsp;</td>
                    </tr>
                </template>
            </table>

            <template v-if="rewards.length > 1 && !displayAll">
                <a href="javascript:void(0);" @click="displayAll = true" class="dashed-link">{{ __("Show more rewards") }} ({{rewards.length - 1}})</a>
                <br>
            </template>

            </template>
            <template v-else>
                <i>{{ __("Unfortunately no rewards are accessible for this balance") }}({{ currentClient.balance }} {{ __("points") }}).</i>
            </template>
        </template>
        <br>
        <button class="btn btn-default" @click="$router.go(-1)">&larr; {{ __("Return to the profile") }}</button>
    </div>
</template>

<script>
    import Util from '../util';

    export default {
        props: ['currentClient'],
        created() {
            this.getAvailableRewards();
        },
        methods: {
            getAvailableRewards: function(e) {
                var params = {
                    user_key: this.currentClient.key,
                    total: this.orderTotal
                };
                Util.queryApi('GET', 'rewards', params, {}, (data) => {
                    this.rewards = data;

                    this.$root.lastOrderTotal = this.orderTotal;
                });
            },

            getLoaderButton: function() {

            },

            acquire: function(reward) {
                this.$root.lastReward = reward;
                this.$router.push(`/client/${this.currentClient.key}/confirm/${reward.id}`);
            },

            clearRewards: function() {
                this.rewards = null;
            }


        },
        data() {
            return {
                orderTotal: null,
                rewards: null,
                displayAll: false
            }
        }
    }
</script>
