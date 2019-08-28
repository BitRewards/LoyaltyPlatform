<template>
    <div class="cashier-interface-container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <template v-if="$root.state.apiToken">
                    <div class="panel-heading main-title" >
                        <span v-html="__('<b>%s</b> Rewards program', partner.title)"></span>
                        <div class="spinner"  v-show="$root.state.loading">
                            <div class="rect1"></div>
                            <div class="rect2"></div>
                            <div class="rect3"></div>
                            <div class="rect4"></div>
                            <div class="rect5"></div>
                        </div>

                        <!--<i class="fa fa-spinner fa-spin"></i>-->
                    </div>

                    <div class="panel-body">
                        <transition v-bind:name="transitionName" mode="out-in">
                            <router-view class="view" v-bind:current-client="currentClient"></router-view>
                        </transition>
                    </div>

                    <router-link class="panel-heading home-link" to="/" v-show="$route.path != '/'">
                        <i class="fa fa-angle-left"></i> {{ __("Return to search") }}
                    </router-link>


                    </template>

                    <template v-else>
                        <div class="panel-heading">{{ __("Get authorized") }}</div>
                        <div class="panel-body">
                            {{ __("Please") }}
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
<script>
    import Util from '../util';

    export default {
        props: ['currentClient'],
        created() {
            Util.queryApi('GET', 'partner', {}, {}, (data) => {
                this.partner = data;

            });
        },
        data() {
            return {
                /*state: this.$root.state,*/
                partner: {
                    title: null
                },
                raw: "asd",
                transitionName: 'slide-left'
            }
        },
        watch: {
            '$route': function(to, from) {
                const toDepth = to.path.split('/').filter(el => el.length).length;
                const fromDepth = from.path.split('/').filter(el => el.length).length;
                if (to.path === '/') {
                    this.transitionName = 'slide-right';
                } else {
                    this.transitionName = toDepth < fromDepth ? 'slide-right' : 'slide-left'
                }
            }
        },
        methods: {
        }


    }
</script>
