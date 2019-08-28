<template>
    <div v-if="coupon">
        <div class="row">
            <div class="col-md-6 col-xs-12 text-center" v-if="coupon.token">
                <h3 v-html="coupon.discountFormatted"></h3>
                <h5>{{coupon.token}}</h5>
                <br>
                <template v-if="coupon.isAvailable">
                    <p>
                        <span class="star">*</span>&nbsp;
                        {{ __("Order amount (before the discount)") }}:
                        <input type="text" class="form-control form-control-inline input-md" @keypress.enter="charge" v-model="amountTotal" placeholder="9000">&nbsp;руб.
                    </p>

                    <input type="text" class="form-control input-lg" @keypress.enter="charge" v-model="comment" :placeholder="__('Comments (optional)')">
                    <br>
                    <button class="btn btn-primary btn-block"
                            v-bind:class="{ 'is-load': $root.state.loading }"
                            v-bind:disabled="$root.state.loading > 0"
                            @click="charge">{{ __("Redeem") }}</button>
                    <br>
                    <div class="col-md-1">&nbsp;</div>
                </template>
                <template v-else>
                    <h3>Not Available</h3>
                    <br><br>
                    <template v-if="currentClient">
                        <button class="btn btn-primary" @click="$router.push('/client/' + currentClient.key)" v-html="__('For profile %s', currentClient.title)"></button>
                    </template>

                </template>
            </div>

            <div class="col-md-5 col-xs-12">
                <table class="table table-striped">
                    <tr>
                        <td>{{ __("Discount") }}</td>
                        <td v-html="coupon.discountFormatted"></td>
                    </tr>
                    <tr v-if="coupon.minAmountTotalStr">
                        <td>{{ __("Min. order") }}</td>
                        <td v-html="coupon.minAmountTotalStr"></td>
                    </tr>
                    <tr v-if="coupon.expiresStr">
                        <td>{{ __("Runs out") }}</td>
                        <td>{{coupon.expiresStr}}</td>
                    </tr>
                    <tr v-if="!coupon.rewardStr">
                        <td>{{ __("Card") }}</td>
                        <td>{{coupon.title}} {{coupon.isFree ? 'free' : ''}}</td>
                    </tr>
                    <tr v-if="coupon.rewardStr">
                        <td>{{ __("Reward") }}</td>
                        <td>{{coupon.rewardStr}}</td>
                    </tr>

                    <tr v-if="coupon.userKey || coupon.owner">
                        <td>{{ __("Owner") }}</td>
                        <td v-if="coupon.userKey"><a href="javascript:void(0);" @click="$router.push('/client/' + coupon.userKey)">{{coupon.owner ? coupon.owner : coupon.userKey}}</a></td>
                        <td v-else>{{coupon.owner ? coupon.owner : "—"}}</td>
                    </tr>
                </table>
            </div>
        </div>

        <br><br>
        <button class="btn btn-default" @click="$router.go(-1)">&larr; {{ __("Back") }}</button>
    </div>
</template>

<script>
    import Util from '../util';

    export default {
        props: ['currentClient'],
        created() {
            if (!this.coupon) {
                this.coupon = this.$root.coupon;
            }
            if (!this.coupon) {
                Util.queryApi('GET', 'coupons/check', {token: this.$route.params.token}, {}, (data) => {
                    this.coupon = data;
                });
            }
            if (!this.currentClient) {
                // we are here from HomePage directly
                this.amountTotal = null;
            } else {
                // we are here from Spend scenario
                this.amountTotal = this.$root.lastOrderTotal;
            }

        },
        methods: {
            charge: function() {
                let params = {
                    token: this.coupon.token,
                    comment: this.comment,
                    amount_total: this.amountTotal
                };
                Util.queryApi('POST', 'coupon/charge', {}, params, (data) => {
                    this.coupon = data;
                });
            },
        },
        data() {
            return {
                coupon: this.$root.coupon,
                amountTotal: null,
                comment: null
            }
        }
    }
</script>
