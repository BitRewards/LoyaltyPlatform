<template>
    <div>
        <template v-if="currentClient">
            <h3>{{currentClient.title}}</h3>

            <template v-if="transactions">
                <template v-if="transactions.length">
                    <table class="table ">
                        <tr class="bold">
                            <th>{{ __("Data") }}</th>
                            <th>{{ __("Points Balance") }}</th>
                            <th>{{ __("Transaction") }}</th>
                            <th>{{ __("Comment") }}</th>
                            <th>{{ __("CMS Order Number") }}</th>
                            <th></th>
                            <th></th>
                        </tr>
                        <tr v-for="transaction in transactions"
                            class="transaction-row"
                            @click="view(transaction)"
                            v-bind:class="{
                                       'row-confirmed': transaction.status == 'confirmed',
                                       'row-rejected': transaction.status == 'rejected',
                                       'row-pending': transaction.status == 'pending',
                                    }">
                            <td class="small">{{transaction.created}}</td>
                            <td v-bind:class="{red: transaction.balanceChange < 0, green: transaction.balanceChange > 0}"><b>{{transaction.balanceChangeStr}}</b> {{ __("points") }}
                                <span v-if="transaction.orderStr"><br />{{transaction.orderStr}}</span>
                            </td>
                            <td>{{transaction.title}}</td>
                            <td>
                                <span v-if="transaction.comment">{{transaction.comment}}<br /></span>
                                <span v-if="transaction.status == 'pending'">{{transaction.statusStr}}</span>
                                <span v-if="transaction.status == 'confirmed'">{{transaction.statusStr}} {{transaction.confirmed}}</span>
                            </td>
                            <td>{{transaction.originalOrderId}}</td>
                            <td>
                                <i class="fa"
                                   v-bind:class="{
                                       'fa-check': transaction.status == 'confirmed',
                                       'fa-ban': transaction.status == 'rejected',
                                       'fa-hourglass-o': transaction.status == 'pending',
                                    }"></i>
                            </td>
                            <td>
                                <button class="btn btn-xs btn-default" @click="view(transaction)">
                                    <i class="fa fa-search-plus"></i>
                                </button>
                            </td>
                        </tr>
                    </table>
                </template>
                <template v-else>
                    <i>{{ __("Customer history is empty") }}.</i>
                </template>
            </template>
            <template v-else>
                <i>{{ __("Customer history is loading...") }}</i>
            </template>

            <br><br>
            <button class="btn btn-default" @click="$router.go(-1)">&larr; {{ __("Return to the profile") }}</button>
        </template>


    </div>
</template>

<style scoped>
    .red {
        color: #ff6400;
    }
    .green {
        color: #008f00;
    }
    .bold {
        font-weight: bold;
    }
    .small {
        font-size: 75%;
    }
    .transaction-row {
        cursor: pointer;
        transition: background 0.5s;
    }
    .transaction-row:hover {
        background: #fafafa !important;
    }
    .transaction-row td {
        vertical-align: middle;
    }
    .row-confirmed {
        background: #f1fff8;
    }
    .row-rejected {
        background: #fff8f9;
    }

    .row-pending {
        background: #faffff;
    }

</style>

<script>
    import Util from '../util';

    export default {
        props: ['currentClient'],
        created() {
            this.init();
            this.$watch('currentClient', this.init);
        },

        methods: {
            view(transaction) {
                this.$root.transaction = transaction;
                this.$router.push('/client/' + this.currentClient.key + '/transaction/' + transaction.id);
            },
            init() {
                if (!this.currentClient) {
                    return;
                }

                if (!this.transactions) {
                    Util.queryApi('GET', 'users/'+this.currentClient.key+'/transactions', {}, {}, (data) => {
                        this.transactions = data;
                    });
                }
            }
        },


        data() {
            return {
                transactions: null
            }
        }
    }
</script>
