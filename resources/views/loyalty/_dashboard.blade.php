<?php
$partner = $partnerPage->viewData->partner;
?>

<Dashboard inline-template>
  <div class="tab-content tab-content_content_dashboard is-hide js-tab-content" data-id="dashboard" data-after-show-callback="DASHBOARD_SHOW">
    <div class="scroller scroller_viewtype_top js-scroller">
      <div class="scroller__in js-scroller-wrapper">
        <div class="tab-content__in tab-content__in_viewtype_mobile-wide">
          <div class="tab-content__body">
            <tip
              aria="<?= __('Close'); ?>"
              category="Onboarding"
              hover="onboardingDashboardHover"
              close="onboardingDashboardCloseClick"
            >
              <template slot="text">
                <?= __('In&nbsp;the &laquo;Referrer dashboard&raquo; section you can see statistics on&nbsp;your charges and actions of&nbsp;your referrals.'); ?>
              </template>

              <template slot="action">
                <br>
                <how-it-work-link category="Onboarding" clickEvent="onboardingDashboardActionClick">
                  <?= \HCustomizations::dashboardOnboardLabel($partnerPage->viewData->partner); ?>
                </how-it-work-link>
              </template>
            </tip>

            <div class="tab-content__header">
              <div class="tab-content__header-in">
                <h2 class="tab-content__title c-text">
                  <?= __('Referrer Dashboard'); ?>
                </h2>
              </div>
            </div>

            
            <div class="statistic">
              <div class="statistic__loader" v-if="!isLoaded">
                <div class="loader">
                  <svg class="loader__icon">
                    <use xlink:href="#loader"></use>
                  </svg>
                  <span class="loader__text">
                    <?= __('Load statistic'); ?>&hellip;
                  </span>
                </div>
              </div>

              <transition name="fade">
                <div class="statistic__in" v-if="isLoaded">
                  <div class="filter">
                    <div class="filter__text">
                      <?= __('Statistics for'); ?>
                      <button class="filter__param c-primary-color c-primary-dropdown" @click.prevent="toggleDashboard">@{{ activeItem }}</button>
                      <ul class="filter__dropdown" v-bind:class="isDropdownShow ? 'is-active': ''">
                        <li class="filter__item" v-for="item, index in filters">
                          <button type="button" class="filter__item-text" v-bind:class="activeItemIndex === index ? 'is-active': ''" @click.prevent="onFilterSelect(item, index)">@{{ item.title }}</button>
                        </li>
                      </ul>
                    </div>

                    <div class="filter__datepicker" v-if="showDatePicker">
                      <div class="filter__datepicker-item">
                        <datepicker :language="ru" :format="customFormatter" :input-class="'c-primary-input calendar-from'" v-model="startDate" @selected="onStartDateSelected"></datepicker>
                      </div>

                      <div class="filter__datepicker-item">
                        <datepicker :language="ru" :format="customFormatter" :input-class="'c-primary-input calendar-to'" v-model="endDate" @selected="onEndDateSelected"></datepicker>
                      </div>
                    </div>
                  </div>

                  <ul class="tiles">
                    <li class="tiles__item">
                      <div class="tile is-important c-primary-tile">
                        <svg class="tile__icon tile__icon_content_cash-in c-primary-fill">
                          <use xlink:href="#cash-in"></use>
                        </svg>
                        <dl class="tile__desc">
                          <dt class="tile__title">
                            <?= \HCustomizations::dashboardTotalCashbackAmountLabel($partnerPage->viewData->partner); ?>
                          </dt>
                          <dd class="tile__content i i_content_currency c-primary-color">
                            <?php if (\HAmount::isCurrencyPrepended($partner->currency)): ?>
                              <span class="i__text"><?= \HAmount::sign($partner->currency); ?></span>
                            <?php endif; ?>
                            @{{ statistic.cashBackAmount }}
                            <?php if (!\HAmount::isCurrencyPrepended($partner->currency)): ?>
                              <span class="i__text"><?= \HAmount::sign($partner->currency); ?></span>
                            <?php endif; ?>
                          </dd>
                        </dl>
                      </div>
                    </li>

                    <li class="tiles__item">
                      <?php if (!$partnerPage->viewData->isWithdrawDisabled): ?>
                        <div class="tile">
                          <svg class="tile__icon tile__icon_content_cash-out">
                            <use xlink:href="#cash-out"></use>
                          </svg>
                          <dl class="tile__desc">
                            <dt class="tile__title">
                              <?= __('Total payment amount'); ?>
                            </dt>
                            <dd class="tile__content i i_content_currency">
                              <?php if (\HAmount::isCurrencyPrepended($partner->currency)): ?>
                                <span class="i__text"><?= \HAmount::sign($partner->currency); ?></span>
                              <?php endif; ?>
                              @{{ statistic.cashBackWithdrawAmount }}
                              <?php if (!\HAmount::isCurrencyPrepended($partner->currency)): ?>
                                <span class="i__text"><?= \HAmount::sign($partner->currency); ?></span>
                              <?php endif; ?>
                            </dd>
                          </dl>
                        </div>
                      <?php endif; ?>
                    </li>

                    <li class="tiles__item">
                      <div class="tile">
                        <svg class="tile__icon tile__icon_content_bag">
                          <use xlink:href="#bag"></use>
                        </svg>
                        <dl class="tile__desc">
                          <dt class="tile__title">
                            <?= __('Total referral purchases amount'); ?>
                          </dt>
                          <dd class="tile__content i i_content_currency">
                            <?php if (\HAmount::isCurrencyPrepended($partner->currency)): ?>
                              <span class="i__text"><?= \HAmount::sign($partner->currency); ?></span>
                            <?php endif; ?>
                            @{{ statistic.totalPurchasesSumAmount }}
                            <?php if (!\HAmount::isCurrencyPrepended($partner->currency)): ?>
                              <span class="i__text"><?= \HAmount::sign($partner->currency); ?></span>
                            <?php endif; ?>
                          </dd>
                        </dl>
                      </div>
                    </li>

                    <li class="tiles__item">
                      <div class="tile">
                        <svg class="tile__icon tile__icon_content_check">
                          <use xlink:href="#check"></use>
                        </svg>
                        <dl class="tile__desc">
                          <dt class="tile__title">
                            <?= __("Referral's average order value"); ?>
                          </dt>
                          <dd class="tile__content i i_content_currency">
                            <?php if (\HAmount::isCurrencyPrepended($partner->currency)): ?>
                              <span class="i__text"><?= \HAmount::sign($partner->currency); ?></span>
                            <?php endif; ?>
                            @{{ statistic.averagePurchaseAmount }}
                            <?php if (!\HAmount::isCurrencyPrepended($partner->currency)): ?>
                              <span class="i__text"><?= \HAmount::sign($partner->currency); ?></span>
                            <?php endif; ?>
                          </dd>
                        </dl>
                      </div>
                    </li>

                    <li class="tiles__item tiles__item_viewtype_small">
                      <div class="tile">
                        <svg class="tile__icon tile__icon_content_shopping-bag">
                          <use xlink:href="#shopping-bag"></use>
                        </svg>
                        <dl class="tile__desc">
                          <dt class="tile__title">
                            <?= __('Total number of confirmed purchases'); ?>
                          </dt>
                          <dd class="tile__content">
                            @{{ statistic.purchasesCount }}
                          </dd>
                        </dl>
                      </div>
                    </li>

                    <li class="tiles__item tiles__item_viewtype_small">
                      <div class="tile">
                        <svg class="tile__icon tile__icon_content_customers">
                          <use xlink:href="#customers"></use>
                        </svg>
                        <dl class="tile__desc">
                          <dt class="tile__title">
                            <?= __('Total number of referrals'); ?>
                          </dt>
                          <dd class="tile__content">
                            @{{ statistic.purchasesCount }}
                          </dd>
                        </dl>
                      </div>
                    </li>

                    <li class="tiles__item tiles__item_viewtype_small">
                      <div class="tile">
                        <svg class="tile__icon tile__icon_content_corner-up">
                          <use xlink:href="#corner-up"></use>
                        </svg>
                        <dl class="tile__desc">
                          <dt class="tile__title">
                              <?= __('Total number of clicks'); ?>
                          </dt>
                          <dd class="tile__content">
                            @{{ statistic.clicksCount }}
                          </dd>
                        </dl>
                      </div>
                    </li>

                    <!-- <li class="tiles__item tiles__item_viewtype_small">
                      <div class="tile">
                        <svg class="tile__icon tile__icon_content_referrals">
                          <use xlink:href="#referrals"></use>
                        </svg>
                        <dl class="tile__desc">
                          <dt class="tile__title">
                            <?= __('Общее количество рефералов'); ?>
                          </dt>
                          <dd class="tile__content">
                            104
                          </dd>
                        </dl>
                      </div>
                    </li>

                    <li class="tiles__item tiles__item_viewtype_small">
                      <div class="tile">
                        <svg class="tile__icon tile__icon_content_corner-up">
                          <use xlink:href="#corner-up"></use>
                        </svg>
                        <dl class="tile__desc">
                          <dt class="tile__title">
                            <?= __('Общее количество переходов'); ?>
                          </dt>
                          <dd class="tile__content">
                            116
                          </dd>
                        </dl>
                      </div>
                    </li>

                    <li class="tiles__item tiles__item_viewtype_small">
                      <div class="tile">
                        <svg class="tile__icon tile__icon_content_transition">
                          <use xlink:href="#transition"></use>
                        </svg>
                        <dl class="tile__desc">
                          <dt class="tile__title">
                            <?= __('Общее количество источников переходов по ссылкам'); ?>
                          </dt>
                          <dd class="tile__content">
                            24
                          </dd>
                        </dl>
                      </div>
                    </li>

                    <li class="tiles__item tiles__item_viewtype_small">
                      <div class="tile">
                        <svg class="tile__icon tile__icon_content_link">
                          <use xlink:href="#hyperlink"></use>
                        </svg>
                        <dl class="tile__desc">
                          <dt class="tile__title">
                            <?= __('Общее количество активных ссылок'); ?>
                          </dt>
                          <dd class="tile__content">
                            12
                          </dd>
                        </dl>
                      </div>
                    </li> -->
                  </ul>
                </div>
              </transition>
            </div>

          </div>
        </div>
        <div class="scroller__bar-wrapper js-scrollbar-container">
          <div class="scroller__bar js-scrollbar"></div>
        </div>
      </div>
    </div>
  </div>
</Dashboard>
