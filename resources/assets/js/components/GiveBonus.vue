<template>
    <div>
        <h3>{{ __("Give points") }}</h3>

        <p>{{ __("Recipient ") }}: <b>{{currentClient.title}}</b>.</p>
        <div v-if="hasCustomBonuses" class="form-group">
            <label for="customBonusActionSelector">{{ __('You can choose Action for which bonus points is issued') }}</label>
            <select id="customBonusActionSelector" v-model="customBonusAction" @change="updateBonusPointsFromCustomAction" class="form-control">
                <option value="0">{{ __('Choose Action') }}</option>
                <option v-for="action in customBonuses" :value="action.id">{{ action.title }}</option>
            </select>
        </div>

        <p>{{ __("How many points do you want to send to the recipient?") }}</p>
        <input type="text" class="form-control input-lg" @keypress.enter="giveBonus" v-model="bonus" :placeholder="__('Points amount')" :readonly="bonusInputDisabled">
        <br>

        <p>{{ __('Reason for issuing a bonus') }}</p>
        <input type="text" class="form-control input-lg" @keypress.enter="giveBonus" v-model="comment" :placeholder="__('Comment')">
        <br>

        <button class="btn btn-primary" @click="giveBonus"
                v-bind:class="{ 'is-load': $root.state.loading }"
                v-bind:disabled="$root.state.loading > 0">
            <i class="fa fa-plus"></i> {{ __("Give points") }}
        </button>
        <p><small>{{ __("The customer will be able to redeem points for purchases") }}.</small></p>


        <br>
        <button class="btn btn-default" @click="$router.go(-1)">&larr; {{ __("Return to the profile") }}</button>
    </div>
</template>

<script>
    import Util from '../util';
    import swal from 'sweetalert';

    export default {
        props: ['currentClient'],

        created() {
            this.loadCustomBonuses()
        },

        methods: {
            giveBonus: function() {
                const payload = {
                    bonus: this.bonus,
                    comment: this.comment,
                    action_id: parseInt(this.customBonusAction, 10)
                }

                Util.queryApi('POST', 'users/'+this.currentClient.key+'/bonus', {}, payload, data => {
                    this.$root.refreshCurrentClient();
                    this.$router.push('/client/' + this.currentClient.key);
                });
            },
            loadCustomBonuses() {
                Util.queryApi('GET', 'actions/custom', {}, {}, data => {
                    this.customBonuses = data
                })
            },
            updateBonusPointsFromCustomAction() {
                const action = this.findCustomBonusAction()

                if (!action) {
                    return this.bonus = null
                }

                this.bonus = action.raw_value
            },
            findCustomBonusAction() {
                return this.customBonuses.find(action => action.id === this.customBonusAction)
            }
        },

        computed: {
            hasCustomBonuses() {
                return this.customBonuses.length !== 0
            },

            bonusInputDisabled() {
                const action = this.findCustomBonusAction()

                if (!action) {
                    return false
                }

                return parseInt(action.is_system, 10) === 0
            }
        },

        data() {
            return {
                bonus: null,
                comment: '',
                customBonuses: [],
                customBonusAction: 0,
            }
        }
    }
</script>
