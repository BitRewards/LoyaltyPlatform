<ul class="list-reset mb-8">
    <li class="leading-tight mb-4 ml-8 text-sm">
        <router-link :to="{name: 'tools-statistic'}"
                     class="text-white text-justify no-underline dim">

            <span class="sidebar-label"><?=__('Tools Statistic'); ?></span>
        </router-link>
    </li>
</ul>
@if(\Bitrewards\ReferralTool\ReferralTool::isEnabled())
<h3 class="flex items-center font-normal text-white mb-6 text-base no-underline">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="sidebar-icon">
        <path fill="var(--sidebar-icon)"
              d="M3 1h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2H3c-1.1045695 0-2-.8954305-2-2V3c0-1.1045695.8954305-2 2-2zm0 2v4h4V3H3zm10-2h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2h-4c-1.1045695 0-2-.8954305-2-2V3c0-1.1045695.8954305-2 2-2zm0 2v4h4V3h-4zM3 11h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2H3c-1.1045695 0-2-.8954305-2-2v-4c0-1.1045695.8954305-2 2-2zm0 2v4h4v-4H3zm10-2h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2h-4c-1.1045695 0-2-.8954305-2-2v-4c0-1.1045695.8954305-2 2-2zm0 2v4h4v-4h-4z"></path>
    </svg>
    <span class="sidebar-label"><?=__('Referral Tool'); ?></span>
</h3>

<ul class="list-reset mb-8">
    <li class="leading-tight mb-4 ml-8 text-sm">
        <router-link :to="{name: 'referral-tool'}"
                     class="text-white text-justify no-underline dim">
        <span class="sidebar-label"><?=__('Dashboard'); ?></span>
        </router-link>
    </li>
    <li class="leading-tight mb-4 ml-8 text-sm">
        <router-link :to="{
                        name: 'index',
                        params: {
                            resourceName: '{{ \App\Nova\Referrer::uriKey() }}'
                        }
                    }" class="text-white text-justify no-underline dim">
            {{ \App\Nova\Referrer::label() }}
        </router-link>
    </li>
    <li class="leading-tight mb-4 ml-8 text-sm">
        <router-link :to="{
                        name: 'index',
                        params: {
                            resourceName: '{{ \App\Nova\PartnerPayments::uriKey() }}'
                        }
                    }" class="text-white text-justify no-underline dim">
            {{ __('Payments') }}
        </router-link>
    </li>
    <li class="leading-tight mb-4 ml-8 text-sm">
        <router-link :to="{
                        name: 'index',
                        params: {
                            resourceName: '{{ \App\Nova\PartnerDeposit::uriKey() }}'
                        }
                    }" class="text-white text-justify no-underline dim">
            {{ __('Balance') }}
        </router-link>
    </li>
    <li class="leading-tight mb-4 ml-8 text-sm">
        <router-link :to="{name: 'settings'}"
                     class=" text-white text-justify no-underline dim">
            <span class="sidebar-label"><?=__('Settings'); ?></span>
        </router-link>
    </li>
</ul>
@endif
