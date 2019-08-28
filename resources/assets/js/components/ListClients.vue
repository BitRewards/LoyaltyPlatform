<template>
    <div>
        <h3>{{ __("Select customer") }}</h3>
        <div class="row" v-for="client in clients" style="margin-bottom: 30px;">
            <div class="col-md-8"
                 v-bind:class="{'col-xs-12': !client.picture, 'col-xs-8': client.picture}">
                <table class="table table-striped">
                    <tr>
                        <td>{{ __("Email") }}</td>
                        <td>{{client.email}}</td>
                    </tr>
                    <tr>
                        <td>{{ __("Phone") }}</td>
                        <td>{{client.phone}}</td>
                    </tr>
                    <tr>
                        <td>{{client.codes.length > 1 ? __('Codes') : __('Code')}}</td>
                        <td>{{(client.codes && client.codes.length) ? client.codes.join(', ') : ''}}</td>
                    </tr>
                    <tr>
                        <td>{{ __("Points Balance") }}</td>
                        <td><big><b>{{client.balance}}</b></big> {{ __("points") }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-xs-4 col-md-4 text-center" v-if="client.picture">
                <img class="client__image" :src="client.picture" :alt="client.name" />
            </div>
            <div class="col-md-12">
                <button class="btn btn-block btn-primary" @click="view(client)">&rarr; {{ __("Open") }}</button>
            </div>

            <br><br><br>
        </div>
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
            view(client) {
                this.$root.currentClient = client;
                this.$router.push('/client/' + this.currentClient.key);
            }
        },


        data() {
            return {
                clients: this.$root.clients || []
            }
        }
    }
</script>
