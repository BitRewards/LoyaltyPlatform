<template>
    <div v-if="currentClient && reward">
        <template v-if="!verified">
        <h4>
            {{ __("Check customer") }}
            <small>/ {{ __("balance") }} {{currentClient.balance}}</small>
        </h4>
        <p>
            <span v-html="__('You are about to spend <b>%s</b> points on «%s»', reward.price, reward.title)"></span>.

            <span v-html="__('After this balance will be <b>%s</b> points', currentClient.balance - reward.price)"></span>.
        </p>
            <br>
        <div class="verifications">
            <div v-if="currentClient.codes && currentClient.codes.length" class="method-wrapper">
                <div class="method">
                    <div class="row">
                        <div class="col-xs-12 col-sm-8 vcenter">
                            <span v-html='__("Check the Rewards card with the number")'></span>
                            <template v-for="(code, i) in currentClient.codes">
                                <b>{{code}}</b>
                                <template v-if="i < currentClient.codes.length - 1">
                                    {{ __("or") }}
                                </template>
                            </template>
                        </div><!--
                        --><div class="col-xs-12 col-sm-4 vcenter">
                        <button class="btn btn-primary btn-block" @click="verified = true">
                            <i class="fa fa-credit-card"></i> {{ __("I checked the card") }}
                        </button>
                    </div>

                    </div>
                </div>
            </div>

            <div v-if="currentClient.phone" class="method-wrapper">
                <div class="method">
                    <div class="row">

                        <template v-if="!smsStatus">
                            <div class="col-xs-12 col-sm-8 vcenter">
                            <span v-html='__("Confirm customer’s phone number <b>%s</b> with an SMS", currentClient.phone)'></span>.
                            </div><!--
                            --><div class="col-xs-12 col-sm-4 vcenter">
                            <button class="btn btn-primary btn-block" @click="sendSms">
                                <i class="fa fa-mobile-phone"></i> {{ __("Send an SMS") }}
                            </button>
                            </div>
                        </template>
                        <template v-else>
                            <template v-if="smsStatus == 'sending'">
                                <div class="col-xs-12 col-sm-8 vcenter">
                                    <i>{{ __("Confirmation SMS being sent ...") }}</i>
                                </div>
                            </template>
                            <template v-if="smsStatus == 'sent' || smsStatus == 'checking'">
                                <div class="col-xs-12 col-sm-8 vcenter">
                                    {{ __("Enter the confirmation code from the SMS") }}:
                                    <input type="tel" class="form-control form-control-inline input-md"
                                           v-model="smsToken"
                                           @keypress.enter="verifySmsCode"
                                           placeholder="6 цифр">
                                    <a href="javascript:void(0);" class="dashed-link" @click="sendSms"><i class="fa fa-refresh"></i> {{ __("resend") }}</a>
                                </div><!--
                                --><div class="col-xs-12 col-sm-4 vcenter">
                                    <button class="btn btn-primary btn-block" @click="verifySmsCode"
                                            v-bind:class="{ 'is-load': smsStatus == 'checking'}">
                                        <i class="fa fa-unlock-alt"></i> {{ __("Check the code") }}
                                    </button>
                                </div>
                            </template>
                        </template>
                    </div>
                </div>
            </div>

            <div v-if="currentClient.name" class="method-wrapper">
                <div class="method">
                    <div class="row">
                        <div class="col-xs-12 col-sm-8 vcenter">
                            <span v-html='__("Ask the customer for an <b>ID</b>, containing the name <nobr><b>%s</b></nobr>", currentClient.name)'></span>.
                        </div><!--
                        --><div class="col-xs-12 col-sm-4 vcenter">
                            <button class="btn btn-primary btn-block" @click="verified = true">
                                <i class="fa fa-id-card-o"></i> {{ __("I checked the ID") }}
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        </template>

        <template v-if="verified">
            <h3>{{ __("Redeem points") }}</h3>
            <br>
            <p v-html='__("Now you can redeem <b>%s</b> points:", reward.price)' ></p>
            <button class="btn btn-primary" @click="acquire"
                    v-bind:class="{ 'is-load': $root.state.loading }"
                    v-bind:disabled="$root.state.loading > 0">
                <i class="fa fa-unlock-alt"></i> <span v-html='__("Redeem %s points", reward.price)'></span>
            </button>
            <br>
            <small v-html="reward.title"></small>
        </template>

        <br><br>
        <button class="btn btn-default" @click="$router.go(-1)">&larr; {{ __("Return to the rewards list page") }}</button>
    </div>
</template>

<style scoped>
    .verifications > div:before {
        content: "или";
        display: block;
        text-align: center;
        color: #999;
        line-height: 60px;
        font-size: 30px;
    }

    .verifications > div:first-child:before {
        display: none;
    }

    .method {
        padding: 10px;
        box-shadow: 0px 2px 10px #ccc
    }
    .no-bold {
        font-weight: 300 !important;
    }
</style>

<script>
    import Util from '../util';

    export default {
        props: ['currentClient', 'currentReward'],
        created() {
            if (!this.reward) {
                this.reward = this.$root.lastReward;
            }
            if (!this.reward) {
                Util.queryApi('GET', 'rewards/'+this.$route.params.rewardId, {}, {}, (data) => {
                    this.reward = data;
                });
            }
        },
        methods: {
            sendSms: function() {
                this.smsStatus = 'sending';
                Util.queryApi('POST', 'users/'+this.currentClient.key+'/sms/send', {}, {}, (result) => {
                    this.smsStatus = 'sent';
                });
            },
            verifySmsCode: function() {
                let params = {
                    token: this.smsToken
                };
                this.smsStatus = 'checking';
                Util.queryApi('POST', 'users/'+this.currentClient.key+'/sms/verify', {}, params, (result) => {
                    if (result.result) {
                        this.verified = true;
                    } else {
                        alert(__("You entered an incorrect code!"));
                    }
                }, () => {
                    this.smsStatus = 'sent';
                });
            },
            acquire: function () {
                let params = {
                    user_key: this.currentClient.key
                };
                Util.queryApi('POST', 'rewards/'+this.reward.id+'/acquire', {}, params, (transaction) => {
                    transaction.fresh = 1;
                    this.$root.transaction = transaction;
                    this.$root.refreshCurrentClient();
                    this.$router.push('/client/' + this.currentClient.key + '/transaction/' + transaction.id);
                });
            }
        },
        data() {
            return {
                orderTotal: null,
                reward: this.$root.lastReward,
                verified: false,
                smsStatus: null,
                smsToken: null
            }
        }
    }
</script>
