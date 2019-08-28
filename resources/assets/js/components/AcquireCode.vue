<template>
    <div>
        <h1>{{ __("Add rewards card") }}</h1>

        <p>{{ __("Customer") }}: <b>{{currentClient.title}}</b></p>

        <input type="text" class="form-control input-lg" @keypress.enter="acquireCode" v-model="token" :placeholder='__("Rewards card number")'>

        <br>
        <div class="row content-columns content-columns_search-client">
            <div class="col-xs-12 col-sm-4 content-column content-column_layout_a">
                <button class="btn btn-primary btn-block"
                        v-bind:class="{ 'is-load': $root.state.loading }"
                        v-bind:disabled="$root.state.loading > 0"
                        @click="acquireCode">
                    <i class="fa fa-plus"></i> {{ __("Add rewards card") }}
                </button>
            </div>
            <div class="col-xs-12 col-sm-4">
                <button class="btn btn-default" @click="$router.go(-1)">&larr; {{ __("Back") }}</button>
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
            acquireCode: function(e) {
                let params = {
                    token: this.token
                };
                Util.queryApi('POST', 'users/'+this.currentClient.key+'/cards', {}, params, (data) => {
                    this.$root.refreshCurrentClient(data);
                    this.$router.push('/client/' + data.key);
                });
            }
        },
        data() {
            return {
                token: null
            }
        }
    }
</script>
