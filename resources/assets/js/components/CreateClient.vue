<template>
    <div>
        <h3>{{ __("New customer") }}</h3>

        <input type="text" class="form-control input-lg" @keypress.enter="create" v-model="token" :placeholder="__('Rewards card number')">
        <p><small>{{ __("The customer will be able to redeem points online or offline using this card number") }}.</small></p>
        <br>
        <input type="text" class="form-control input-lg" @keypress.enter="create" v-model="name" :placeholder="__('First and Last name')">
        <p><small>{{ __("Will be used for search in the Rewards Program") }}.</small></p>
        <br>
        <input type="text" class="form-control input-lg" @keypress.enter="create" v-model="phone" :placeholder="__('Phone')">
        <p><small>{{ __("Will be used for search in the Rewards Program") }}.</small></p>
        <br>
        <input type="text" class="form-control input-lg" @keypress.enter="create" v-model="email" :placeholder="__('Email')">
        <p><small>{{ __("Will be used for search in the Rewards Program") }}.</small></p>

        <br>
        <button class="btn btn-primary" @click="create"
                v-bind:class="{ 'is-load': $root.state.loading }"
                v-bind:disabled="$root.state.loading > 0">
            <i class="fa fa-plus"></i> {{ __("Add New Customer") }}
        </button>
    </div>
</template>

<script>
    import Util from '../util';
    import swal from 'sweetalert';

    export default {
        props: ['currentClient'],
        methods: {
            create: function(e) {
                let data = {
                    token: this.token,
                    phone: this.phone,
                    name: this.name,
                    email: this.email
                };
                Util.queryApi('POST', 'users', {}, data, (data) => {
                    this.$root.currentClient = data;
                    this.$router.push('/client/' + data.key);
                });
            }
        },
        data() {
            return {
                token: null,
                phone: null,
                name: null,
                email: null
            }
        }
    }
</script>
