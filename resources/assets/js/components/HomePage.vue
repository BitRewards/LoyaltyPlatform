<template>
    <div>
        <h1>{{ __("Client Search") }}</h1>

        <input type="text" class="form-control input-lg input-smaller-placeholder-mobile"  @keypress.enter="search" v-model="query"
               :placeholder="__('Name / email / phone / Rewards card number / promocode')">

        <br>
        <div class="row content-columns content-columns_search-client">
            <div class="col-xs-12 col-sm-4 content-column content-column_layout_a">
                <button class="btn btn-primary btn-block"
                        v-bind:class="{ 'is-load': $root.state.loading }"
                        v-bind:disabled="$root.state.loading > 0"
                        @click="search">
                    <i class="fa fa-search"></i> {{ __("Find Customer") }}
                </button>
            </div>
            <div class="col-xs-12 col-sm-4">
                <button class="btn btn-default btn-block" @click="$router.push('/client-create')"><i class="fa fa-plus"></i> {{ __("Add New Customer") }}</button>
            </div>
        </div>

    </div>
</template>

<script>
    import Util from '../util';
    import swal from 'sweetalert';

    export default {
        props: ['currentClient'],
        methods: {
            search: function(e) {

                let responsesCount = 0;
                let responses = {};

                let onEverythingFinished = () => {
                    if (responses['coupon']) {
                        let data = responses['coupon'];
                        this.$root.coupon = data;
                        this.$router.push('/coupon/' + data.token);
                    } else {
                        let data = responses['user'];
                        if (data && data.length > 1) {
                            this.$root.clients = data;
                            this.$router.push('/list-clients');
                        } else if (data && data.length == 1) {
                            this.$root.currentClient = data[0];
                            this.$router.push('/client/' + data[0].key);
                        } else {
                            swal({
                                    title: __('Customer not found!'),
                                    text: __('Go to registration?'),
                                    type: "warning",
                                    showCancelButton: true,
                                    confirmButtonColor: '#5EC1E7',
                                    confirmButtonText: __('Yes'),
                                    cancelButtonText: __('No'),
                                    closeOnConfirm: true
                                })
                                .then((create) =>  {
                                    if (create) {
                                        this.$router.push('/client-create');
                                    }
                                });
                        }
                    }
                };

                let accumulateResponse = (response, context) => {
                    responses[context] = response;
                    responsesCount++;
                    if (responsesCount == 2) {
                        onEverythingFinished();
                    }
                };

                Util.queryApi('GET', 'search/users', {query: this.query}, {}, (data) => {
                    accumulateResponse(data, 'user');
                }, () => {
                    // accumulateResponse(null, 'user');
                });

                Util.queryApi('GET', 'coupons/check', {token: this.query}, {}, (data) => {
                    accumulateResponse(data, 'coupon');
                }, () => {
                    // accumulateResponse(null, 'coupon');
                });
            }
        },
        data() {
            return {
                query: null
            }
        }
    }
</script>
