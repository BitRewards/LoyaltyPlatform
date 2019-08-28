@if (Auth::check())
    <!-- Left side column. contains the sidebar -->
    <aside class="main-sidebar">
      <!-- sidebar: style can be found in sidebar.less -->
      <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
          <div class="pull-left image">
            <img src="//placehold.it/160x160/00a65a/ffffff/&text={{ empty(Auth::user()->name) ? 'GIFTD' : mb_substr(Auth::user()->partner ? Auth::user()->partner->title : Auth::user()->name, 0, 1) }}" class="img-circle" alt="<?= Auth::user()->partner ? Auth::user()->partner->title : Auth::user()->name; ?>">
          </div>
          <div class="info">
            <p class="dont-break-out">{{ Auth::user()->partner ? Auth::user()->partner->title : Auth::user()->name }}</p>
            <a href="#"><i class="fa fa-circle text-success"></i> <?= __('Online'); ?></a>
          </div>
        </div>
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
          <!-- ================================================ -->
          <!-- ==== Recommended place for admin menu items ==== -->
          <!-- ================================================ -->
          @can('partner')

            <li class="active treeview">
              <a href="#">
                <i class="fa fa-shopping-cart"></i> <span><?=__('Loyalty program'); ?></span>
                <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
              </a>
              <ul class="treeview-menu" style="display: none;">
                <li><a href="{{ url('admin/action') }}"><i class="fa fa-hand-pointer-o"></i> <span>{{  __('Actions') }}</span></a></li>
                <li><a href="{{ url('admin/reward') }}"><i class="fa fa-usd"></i> <span>{{ __('Rewards') }}</span></a></li>
                <li><a href="{{ url('admin/transaction') }}"><i class="fa fa-random"></i> <span>{{ __('Transactions') }}</span></a></li>
                <li><a href="{{ url('admin/code') }}"><i class="fa fa-key"></i> <span>{{  __('Promo codes') }}</span></a></li>
                <li><a href="{{ url('admin/user') }}"><i class="fa fa-users"></i> <span>{{  __('Users') }}</span></a></li>
                <li><a href="{{ url('admin/help-items') }}"><i class="fa fa-list"></i> <span>{{ __('FAQ') }}</span></a></li>
                <li><a href="{{ url('admin/cashier-users') }}"><i class="fa fa-money"></i> <span>{{ __('Cashiers') }}</span></a></li>
                @can('admin')
                  <li><a href="{{ url('admin/partner') }}"><i class="fa fa-building"></i> <span>Партнеры</span></a></li>
                @endcan
              </ul>
            </li>

            @can('admin')
            <li class="active treeview">
              <a href="#">
                <i class="fa fa-mobile"></i> <span><?=__('Mobile App'); ?></span>
                <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
              </a>
              <ul class="treeview-menu" style="display: none;">
                <li><a href="{{ url('admin/specialOfferAction') }}"><i class="fa fa-gift"></i> <span>{{ __('Special offer actions') }}</span></a></li>
                <li><a href="{{ url('admin/specialOfferReward') }}"><i class="fa fa-gift"></i> <span>{{ __('Special offer rewards') }}</span></a></li>
              </ul>
            </li>
            @endcan

            @can('admin')
            <li class="active treeview">
              <a href="#">
                <i class="fa fa-credit-card"></i> <span><?=__('Referral program'); ?></span>
                <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
              </a>
              <ul class="treeview-menu" style="display: none;">
                <li><a href="{{ url('admin/merchant') }}"><i class="fa fa-group"></i> <span>{{  __('Merchants') }}</span></a></li>
                <li><a href="{{ url('admin/referrer') }}"><i class="fa fa-child"></i> <span>{{  __('Referrers') }}</span></a></li>
                <li><a href="{{ url('admin/referrerWithdraw') }}"><i class="fa fa-credit-card"></i> <span>{{  __('Payments') }}</span></a></li>
                <li><a href="{{ url('admin/referrerTransaction') }}"><i class="fa fa-random"></i> <span>{{  __('Transactions') }}</span></a></li>
                <li><a href="{{ url('admin/partnerDeposit') }}"><i class="fa fa-usd"></i> <span>{{  __('Merchant bills') }}</span></a></li>
              </ul>
            </li>
            @endcan

            @cannot('admin')
              <li><a href="{{ url('admin/reports') }}"><i class="fa fa-users"></i> <span>{{ __('Reports') }}</span></a></li>
              <?php if (!Auth::user()->partner->isBitrewardsEnabled() && Auth::user()->partner->mainAdministrator):  ?>
              <li><a href="{{ url('admin/cashier') }}?api_token={{ Auth::user()->partner->mainAdministrator->api_token }}" target="_blank"><i class="fa fa-usd"></i> <span><?= __('Cashier Interface'); ?></span></a></li>
              <?php endif; ?>
              <?php if (Auth::user()->partner->isBitrewardsEnabled()):  ?>
              <li><a href="{{ url('admin/wallet') }}"><i class="fa fa-btc"></i> <span>{{ __('Wallet') }}</span></a></li>
              <li><a href="{{ url('admin/bitrewards-settings') }}"><i class="fa fa-gear"></i> <span>{{ __('Bitrewards settings') }}</span></a></li>
              <?php endif; ?>
            @endcannot

            @cannot('admin')
              <li class="treeview">
                <a href="#">
                  <i class="fa fa-th"></i>
                  <span>{{ __('Miscellaneous') }}</span>
                  <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                  </span>
                </a>
                <ul class="treeview-menu">
                <?php if (!Auth::user()->partner->hasEventbrite()):  ?>
                  <li><a href="{{ app(App\Services\EventbriteService::class)->getOauthUrl() }}" ><i class="fa fa-calendar"></i><span><b><?= __('Connect Eventbrite'); ?></b></span></a></li>
                <?php else: ?>
                  <li><a href="{{ route('admin.eventbrite.unbind') }}" ><i class="fa fa-remove"></i> <span><b><?= __('Unbind Eventbrite'); ?></b></span></a></li>
                <?php endif; ?>
                  <?php if (false) {
    ?>
                  <li class="treeview">
                    <a href="#">
                      <i class="fa fa-envelope"></i>
                      <span>{{ __('Email examples') }}</span>
                      <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                      </span>
                    </a>
                    <ul class="treeview-menu">
                      <li><a target="_blank" href="{{ route('admin.emails.balanceChanged', ['partner_key' => Auth::user()->partner->key]) }}"><i class="fa fa-usd"></i> {{ __('Balance change') }}</a></li>
                    </ul>
                  </li>
                  <?php
} ?>
                </ul>
              </li>
            @endcannot
          @endcan

          @can('admin')
            <li><a href="{{ url('admin/log') }}"><i class="fa fa-cog"></i> <span>Отладка</span></a></li>
          @endcan
          <!-- ======================================= -->
          <li class="header">{{ trans('backpack::base.user') }}</li>
          <li><a href="{{ url('admin/logout') }}"><i class="fa fa-sign-out"></i> <span>{{ trans('backpack::base.logout') }}</span></a></li>
        </ul>
      </section>
      <!-- /.sidebar -->
    </aside>
@endif
