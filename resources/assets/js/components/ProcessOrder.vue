<template>
    <div>
        <h3>{{ __("Reward an Order") }}</h3>
        <p v-html="__('To receive a points reward for the order, add this number in to the <nobr><b><big>order comments:</big></b></nobr>', currentClient.title)"></p>
        <h1 class="code" style="margin-top: 0;">{{currentClient.tracking}}</h1>
        <small>{{ __("Comments can contain additional information. The system will interpret and take account of everything") }}.</small>
        <br>
        <br>
        <button class="btn btn-default" @click="$router.go(-1)">&larr; {{ __("Return to the profile") }}</button>

    </div>
</template>

<script>
    import Util from '../util';

    export default {
        props: ['currentClient'],
        methods: {
            onEnterOrderTotal() {

            }
        },

    }
</script>
