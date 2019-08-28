<template>
    <div v-if="transaction">
        <div class="row">
            <div class="col-md-6 col-xs-12 text-center" v-if="transaction.canPromoCodeBeApplied">
                <p>
                    {{ __("To apply a discount to an order, please use this promo code:") }}
                </p>
                <h3>{{transaction.promoCode}}</h3>
                <countdown
                        v-if="transaction.promoCodeExpiresInSeconds"
                        :seconds-left="transaction.promoCodeExpiresInSeconds"
                        finished-text="Истек!"
                        ></countdown>
                <br>
                <button class="btn btn-primary btn-block" @click="proceed">{{ __("I've used a promo code for an order") }}</button>
                <br>
                <button class="btn btn-default btn-sm btn-block" @click="manual">{{ __("Manually accept a promo code") }}</button>
                <br>

                <div class="col-md-1">&nbsp;</div>
            </div>

            <div class="col-md-5 col-xs-12">
                <table class="table table-striped">
                    <tr>
                        <td>ID</td>
                        <td>{{transaction.id}}</td>
                    </tr>
                    <tr>
                        <td>{{ __("Data") }}</td>
                        <td>{{transaction.created}}</td>
                    </tr>
                    <tr>
                        <td>{{ __("Description") }}</td>
                        <td v-html="transaction.title"></td>
                    </tr>
                    <tr>
                        <td>{{ __("Change in balance") }}</td>
                        <td>{{transaction.balanceChangeStr}} {{ __("points") }}</td>
                    </tr>
                    <tr>
                        <td>{{ __("Status") }}</td>
                        <td>{{transaction.statusStr}}</td>
                    </tr>
                </table>
            </div>
        </div>


        <template v-if="!transaction.fresh">
            <br><br>
            <button class="btn btn-default" @click="$router.go(-1)">&larr; {{ __("Return to history") }}</button>
        </template>
    </div>
</template>

<script>
    import Util from '../util';

    export default {
        props: ['currentClient'],
        created() {
            if (!this.transaction) {
                this.transaction = this.$root.transaction;
            }
            if (!this.transaction) {
                Util.queryApi('GET', 'transactions/'+this.$route.params.transactionId, {}, {}, (data) => {
                    this.transaction = data;
                });
            }
        },
        methods: {
            manual: function() {
                this.$router.push('/client/' + this.currentClient.key + '/coupon/' + this.transaction.promoCode);
            },
            cancel: function() {
                swal({
                    title: __('Cancel the promo code?'),
                    text: __('If you give a discount using another method (not through a promo code), then the promo code ought to be canceled.'),
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: '#5EC1E7',
                    confirmButtonText: __('Yes'),
                    cancelButtonText: __('No'),
                    closeOnConfirm: true
                })
                .then(() =>  {
                    Util.queryApi('POST', 'transactions/'+this.transaction.id+'/cancelPromoCode', {}, {}, (data) => {
                        alert(__('Promo code is successfully canceled'));
                        this.$router.push('/client/' + this.currentClient.key);
                    });
                });
            },
            proceed: function(e) {
                this.$router.push('/client/' + this.currentClient.key);
            }
        },
        data() {
            return {
                transaction: this.$root.transaction
            }
        }
    }
</script>
