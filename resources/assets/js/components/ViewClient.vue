<template>
    <div>
        <template v-if="currentClient">
        <h3>{{currentClient.title}}</h3>
        <div class="row client">
            <div class="col-sm-8 col-xs-12">
                <table class="table table-striped">
                    <tr>
                        <td>Email</td>
                        <td>{{currentClient.email}}</td>
                    </tr>
                    <tr>
                        <td>{{ __("Phone") }}</td>
                        <td>{{currentClient.phone}}</td>
                    </tr>
                    <tr>
                        <td>{{currentClient.codes.length > 1 ? __('Rewards cards') : __('Rewards card')}}</td>
                        <td>
                            <button class="btn btn-default btn-xs" @click="bindCode" :title="__('Attach a code')"><i class="fa fa-plus"></i></button>

                            <div v-if="hasCodesAttached" style="margin-top: 10px">
                                <ul class="list-group">
                                    <li v-for="code in currentClient.codes" class="list-group-item">
                                        <a href="javascript:;" class="detach-code-btn btn btn-default btn-xs pull-right" @click="detachCode(code)" :disabled="detachingCode">
                                            <i class="fa fa-times"></i>
                                        </a>
                                        {{ code }}
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ __("Points Balance") }}</td>
                        <td><big><b>{{currentClient.balance}}</b></big> {{ __("points") }}</td>
                    </tr>
                </table>
            </div>
            <div class="hidden-xs col-sm-4 text-center"  v-if="currentClient.picture">
                <img class="client__image" :src="currentClient.picture" :alt="currentClient.name" />
            </div>
        </div>

            <br>

        <div class="row content-columns_client">
            <div class="col-md-3 col-xs-12 button-margin-bottom"  v-if="currentClient.balance > 0">
                <button class="btn btn-block btn-primary" @click="spend"><i class="fa fa-percent"></i> {{ __("Redeem points") }}</button>
            </div>
            <div class="col-md-3 col-xs-12 button-margin-bottom">
                <button class="btn btn-block btn-default" @click="processOrder"><i class="fa fa-shopping-bag"></i> {{ __("Invoice") }}</button>
            </div>
            <div class="col-md-3 col-xs-12 button-margin-bottom" >
                <button class="btn btn-block btn-default" @click="giveBonus"><i class="fa fa-plus"></i> {{ __("Give points") }}</button>
            </div>
            <div class="col-md-3 col-xs-12 button-margin-bottom">
                <button class="btn btn-block btn-default" @click="history"><i class="fa fa-list"></i> {{ __("History") }}</button>
            </div>
        </div>

        <div class="row">
            <div class="visible-xs col-xs-12 text-center"  v-if="currentClient.picture">
                <img class="client__image" :src="currentClient.picture" :alt="currentClient.name" />
            </div>
        </div>

        </template>
    </div>
</template>

<style scoped>
    .button-margin-bottom {
        margin-bottom: 10px;
    }
</style>

<script>
    import Util from '../util';

    export default {
        props: ['currentClient'],
        created() {
        },

        methods: {
            spend() {
                this.$router.push('/client/' + this.currentClient.key + '/spend');
            },
            processOrder() {
                this.$router.push('/client/' + this.currentClient.key + '/process-order');
            },
            giveBonus() {
                this.$router.push('/client/' + this.currentClient.key + '/give-bonus');
            },
            history() {
                this.$router.push('/client/' + this.currentClient.key + '/history');
            },
            bindCode() {
                this.$router.push('/client/' + this.currentClient.key + '/acquire-code');
            },
            detachCode(code) {
                if (!confirm(__("Are you sure you want to detach this card from the user? The bonuses associated with this card will be debited from the user's balance!"))) {
                    return alert(__('Operation was not performed'))
                }

                this.detachingCode = true

                Util.queryApi('DELETE', `users/${this.currentClient.key}/cards/${code}`, {}, {}, data => {
                    this.$root.refreshCurrentClient(data)
                    this.$router.push('/client/' + data.key)
                }, () => {
                    this.detachingCode = false
                })
            }
        },

        computed: {
            hasCodesAttached() {
                return this.currentClient && this.currentClient.codes && this.currentClient.codes.length > 0
            }
        },

        data() {
            return {
                // client: this.$root.currentClient || {}
                detachingCode: false,
            }
        }
        /*watch: {
            '$root.currentClient': function() {
                this.client = this.$root.currentClient;
                console.log('asd');
            }
        }*/
    }
</script>
