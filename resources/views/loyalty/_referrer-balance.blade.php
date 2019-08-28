<?php
/**
 * @var \App\DTO\PartnerPageData
 */
$referrerBalance = $partnerPage->referrerBalance;
$partnerSettings = $partnerPage->partner->partnerSettings;
?>
<?php if ($referrerBalance) {
    ?>
  <referrer-balance inline-template>
    <div class="tab-content tab-content_content_referrer-balance is-hide js-tab-content js-updatable" data-id="referrer-balance">
      <div class="tab-content__in tab-content__in_content_referrer-balance">
        <div class="scroller scroller_viewtype_top">
          <div class="scroller__in">
            <tip
              aria="<?= __('Close'); ?>"
              category="Onboarding"
              hover="onboardingBalanceHover"
              close="onboardingBalanceClose"
            >
              <template slot="text">
                <?= \HCustomizations::referrerBalanceTipMessage($partnerPage->viewData->partner); ?>
              </template>
            </tip>

            <div class="tab-content__header">
              <Tab inline-template>
                <dl class="tab">
                  <dt class="tab__title i i_content_bold c-tab-title" v-bind:class="activeIndex === 0 ? 'is-active' : ''" @click="onClick(0)">
                    <?= __('Current balance'); ?>
                    <span class="info info_position_bottom" data-text="<?= htmlspecialchars(__('&laquo;Current Balance&raquo;&nbsp;&mdash; is&nbsp;all your earnings, without all payments and funds on&nbsp;the payment.')); ?>"><svg class="info__icon"><use xlink:href="#question"></use></svg></span>
                    <div class="i__text"><?=$referrerBalance->currentBalance; ?></div>
                  </dt>
                  <dd class="tab__content">
                    <div class="content-columns content-columns_content_balance">
                      <div class="content-column content-column_layout_a">
                        <how-it-work-link class="i i_content_bold" category="Onboarding" clickEvent="onboardingBalanceActionClick">
                          <span class="i__text"><?= __('How to increase your balance?'); ?></span>
                        </how-it-work-link>
                        <div class="desc-table">
                          <?php if (!$partnerPage->viewData->isWithdrawDisabled): ?>
                            <dl class="desc-table__item">
                              <dt class="desc-table__title">
                                <?= __('Available for withdraw'); ?>
                                <span class="info" data-text="<?= htmlspecialchars(\HCustomizations::referrerBalanceAvailableFundsTipMessage($partnerPage->viewData->partner)); ?>"><svg class="info__icon"><use xlink:href="#question"></use></svg></span>
                              </dt>

                              <dd class="desc-table__content">
                                <?=$referrerBalance->availableForWithdraw; ?>
                              </dd>
                            </dl>

                            <dl class="desc-table__item">
                              <dt class="desc-table__title">
                                <?= __('On payment'); ?>
                                <span class="info" data-text="<?= htmlspecialchars(__('&laquo;On&nbsp;payment&raquo;&nbsp;&mdash; is&nbsp;the amount that is&nbsp;on&nbsp;withdrawal right now. The payment process takes up&nbsp;to&nbsp;7 days. Find all withdrawal transactions in&nbsp;the list of&nbsp;withdrawal transactions on&nbsp;the current &laquo;Balance&raquo; page.')); ?>"><svg class="info__icon"><use xlink:href="#question"></use></svg></span>
                              </dt>

                              <dd class="desc-table__content">
                                <?=$referrerBalance->blocked; ?>
                              </dd>
                            </dl>
                          <?php endif; ?>

                          <dl class="desc-table__item">
                            <dt class="desc-table__title">
                              <?= __('Total paid'); ?>
                              <span class="info" data-text="<?= htmlspecialchars(__('&laquo;Total paid&raquo;&nbsp;&mdash; is&nbsp;the amount of&nbsp;funds already withdrawn for the entire period. Find all withdrawals in&nbsp;the list of&nbsp;withdrawal transactions on&nbsp;the current &laquo;Balance&raquo; page.')); ?>"><svg class="info__icon"><use xlink:href="#question"></use></svg></span>
                            </dt>

                            <dd class="desc-table__content">
                              <?=$referrerBalance->paid; ?>
                            </dd>
                          </dl>

                          <dl class="desc-table__item">
                            <dt class="desc-table__title">
                              <?= __('Total earned'); ?>
                               <span class="info" data-text="<?= htmlspecialchars(\HCustomizations::referrerBalanceTotalEarnedTipMessage($partnerPage->viewData->partner)); ?>"><svg class="info__icon"><use xlink:href="#question"></use></svg></span>
                            </dt>

                            <dd class="desc-table__content">
                              <?=$referrerBalance->earned; ?>
                            </dd>
                          </dl>
                        </div>
                        <?php if (!$partnerPage->viewData->isWithdrawDisabled): ?>
                          <ul class="tab-content__action tab-content__action_viewtype_block tab-content__action_content_withdraw">
                            <li class="tab-content__action-item">
                              <button type="button" class="button button_type_block button_viewtype_primary c-primary-button" @click="showWithdraw">
                                <span class="button__text">
                                  <?= __('Withdraw funds'); ?>
                                </span>
                              </button>
                            </li>
                          </ul>
                        <?php endif; ?>
                      </div>

                      <?php if (!$partnerPage->viewData->isWithdrawDisabled): ?>
                        <div class="content-column content-column_layout_b">
                          @include('/loyalty/_withdraw-conditions', ['action' => false])
                        </div>
                      <?php endif; ?>
                    </div>
                  </dd>
                  <?php if (!$partnerPage->viewData->isMyCouponsHidden): ?>
                    <dt class="tab__title i i_content_bold c-tab-title" v-bind:class="activeIndex === 1 ? 'is-active' : ''" @click="onClick(1)">
                      <?= __('My coupons'); ?>
                      <div class="i__text"><?= __('%count% {coupon|coupons|coupons}', ['count' => $partnerPage->activeCouponCount]); ?></div>
                    </dt>
                    <dd class="tab__content">
                      @if (empty($partnerPage->couponList))
                        <div class="reward-empty">
                          <svg class="reward-empty__icon">
                            <use xlink:href="#discount"></use>
                          </svg>
                          <span class="reward-empty__text">
                            <?= __('There are currently no rewards'); ?>
                          </span>
                        </div>
                        @else
                          <ul class="list list_content_reward js-reward-list js-updatable">
                            @foreach ($partnerPage->couponList as $coupon)
                              @if($coupon instanceof \App\DTO\SavedCouponData)
                                @include('loyalty/_saved-coupon-row')
                              @else
                                @include('loyalty/_transaction-row', ['transaction' => $coupon])
                              @endif
                            @endforeach
                          </ul>
                        @endif
                    </dd>
                  <?php endif; ?>
                </dl>
              </Tab>
            </div>

            <div class="tab-content__body">
              <?php if ($referrerBalance->withdrawTransactions->count()): ?>
                <div class="transaction">

                  <h3 class="transaction__title">
                    <?=__('Funds withdraw transactions:'); ?>
                  </h3>

                  <div class="transaction__body">

                    <table class="table table_content_transactions table_viewtype_small-fields">
                      <thead class="table__head">
                        <tr class="table__row">
                          <th class="table__heading">
                            <?=__('ID'); ?>
                          </th>
                          <th class="table__heading">
                            <?=__('Date/time'); ?>
                          </th>
                          <th class="table__heading">
                            <?=__('Recipient name'); ?>
                          </th>
                          <th class="table__heading">
                            <?=__('Card number'); ?>
                          </th>
                          <th class="table__heading">
                            <?=__('Amount'); ?>
                          </th>
                          <th class="table__heading">
                            <?=__('Fee'); ?>
                          </th>
                          <th class="table__heading">
                            <?=__('Sent'); ?>
                          </th>
                          <th class="table__heading">
                            <?=__('Confirmed'); ?>
                          </th>
                          <th class="table__heading">
                            <?=__('Status'); ?>
                          </th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php foreach ($referrerBalance->withdrawTransactions as $transaction): ?>
                        <tr class="table__row">
                          <td class="table__cell" data-label="<?=__('ID'); ?>">
                            <div class="table__cell-text">
                              <?=$transaction->id; ?>
                            </div>
                          </td>

                          <td class="table__cell" data-label="<?=__('Date/time'); ?>">
                            <div class="table__cell-text">
                              <span class="transaction__date">
                                <?=$transaction->createdAtDate; ?>
                              </span>
                              <span class="transaction__meta">
                                <?=$transaction->createdAtTime; ?>
                              </span>
                            </div>
                          </td>

                          <td class="table__cell" data-label="<?=__('Recipient name'); ?>">
                            <div class="table__cell-text">
                                <?=$transaction->firstName; ?> <?=$transaction->lastName; ?>
                            </div>
                          </td>

                          <td class="table__cell" data-label="<?=__('Card number'); ?>">
                            <div class="table__cell-text">
                                <?=$transaction->maskedCardNumber; ?>
                            </div>
                          </td>

                          <td class="table__cell" data-label="<?=__('Amount'); ?>">
                            <div class="table__cell-text">
                                <?=$transaction->amount; ?>
                            </div>
                          </td>

                          <td class="table__cell" data-label="<?=__('Fee'); ?>">
                            <div class="table__cell-text">
                              <span class="transaction__desc">
                                <?=$transaction->fee; ?>
                              </span>
                              <span class="transaction__meta">
                                <?=$transaction->feeStr; ?>
                              </span>
                            </div>
                          </td>

                          <td class="table__cell" data-label="<?=__('Sent'); ?>">
                            <div class="table__cell-text">
                              <?=$transaction->sent; ?>
                            </div>
                          </td>

                          <td class="table__cell" data-label="<?=__('Confirmed'); ?>">
                            <div class="table__cell-text">
                              <span class="transaction__date">
                                <?=$transaction->confirmedAtDate; ?>
                              </span>
                              <span class="transaction__meta">
                                <?=$transaction->confirmedAtTime; ?>
                              </span>
                            </div>
                          </td>

                          <td class="table__cell" data-label="<?=__('Status'); ?>">
                            <div class="table__cell-text">
                              <span class="transaction__status transaction__status_viewtype_wait">
                                <?=$transaction->status; ?>
                              </span>
                            </div>
                          </td>
                        </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>

                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <div class="box box_viewtype_fullscreen" v-if="isWithdrawShow">
        <div class="box__in">
          <h3 class="heading heading_level_3 i i_content_brand">
            <?= __('Available for withdrawal balance:'); ?> <span class="i__text c-primary-color"><?=$referrerBalance->availableForWithdraw; ?></span>
          </h3>
          <p class="box__subtitle form-remark form-remark_viewtype_left">
            <?= __('Please indicate your Visa or Mastercard bank card number for which you want to receive funds. Specify only real data'); ?>
          </p>

          <div class="content-columns content-columns_content_balance">
            <div class="content-column content-column_layout_a">
              <form class="form form_content_referrer-withdraw" @submit.prevent="validateBeforeSubmit">
                <fieldset class="form-field-group">
                  <ul class="form-fields-list">
                    <li class="form-fields-list__item">
                      <div class="field-composition">
                        <div class="field-composition__content">
                          <div class="form-field-box">
                            <input type="text" class="form-field form-field_type_text is-small c-primary-input" name="withdraw_card" id="withdraw_card" autocomplete="cc-number" v-model="card" v-validate="'required|credit_card'" required>
                            <label class="form-field-label form-field-label_viewtype_float c-primary-label" for="withdraw_card">
                              <?= __('Card number'); ?>
                            </label>
                          </div>
                        </div>
                      </div>
                    </li>

                    <li class="form-fields-list__item">
                      <div class="field-composition">
                        <div class="field-composition__content">
                          <div class="form-field-box">
                            <input type="text" class="form-field form-field_type_text is-small c-primary-input" name="withdraw_firstname" id="withdraw_firstname" v-model="firstname" v-validate="'required'" required>
                            <label class="form-field-label form-field-label_viewtype_float c-primary-label" for="withdraw_firstname">
                              <?= __('First name'); ?>
                            </label>
                          </div>
                        </div>
                      </div>
                    </li>

                    <li class="form-fields-list__item">
                      <div class="field-composition">
                        <div class="field-composition__content">
                          <div class="form-field-box">
                            <input type="text" class="form-field form-field_type_text is-small c-primary-input" name="withdraw_secondname" id="withdraw_secondname" v-model="secondname" v-validate="'required'" required>
                            <label class="form-field-label form-field-label_viewtype_float c-primary-label" for="withdraw_secondname">
                              <?= __('Second name'); ?>
                            </label>
                          </div>
                        </div>
                      </div>
                    </li>

                    <li class="form-fields-list__item">
                      <div class="field-composition">
                        <div class="field-composition__content">
                          <div class="form-field-box">
                            <input type="text" class="form-field form-field_type_text is-small c-primary-input" name="withdraw_amount" id="withdraw_amount" v-model="amount" v-validate="'required|between:<?=$partnerSettings->fiatMinWithdrawAmount; ?>,<?=$partnerSettings->fiatMaxWithdrawAmount; ?>'" required>
                            <label class="form-field-label form-field-label_viewtype_float c-primary-label" for="withdraw_amount">
                              <?= __('Set the withdraw amount'); ?>
                            </label>
                          </div>
                        </div>
                      </iv>
                    </li>

                    <li class="form-fields-list__item">
                      <dl class="field-composition field-composition_content_total">
                        <dt class="field-composition__title">
                          <div class="form-field-label i i_content_brand">
                            <?= __('%fee% fee for transaction', ['fee' => "<span class='i__text c-primary-color'>{$partnerSettings->fiatWithdrawFee}</span>"]); ?>
                          </div>
                        </dt>

                        <dd class="field-composition__content">
                          <div class="form-field-box" v-cloak>
                            @{{ fee }}
                          </div>
                        </dd>
                      </dl>
                    </li>

                    <li class="form-fields-list__item form-fields-list__item_viewtype_total">
                      <dl class="field-composition field-composition_content_total">
                        <dt class="field-composition__title">
                          <div class="form-field-label">
                            <?= __('You will get'); ?>
                          </div>
                        </dt>

                        <dd class="field-composition__content">
                          <div class="form-field-box" v-cloak>
                            @{{ total }}
                          </div>
                        </dd>
                      </dl>
                    </li>

                    @include('/loyalty/_privacy', ['id' => 'privacy_policy_withdraw'])

                    <li class="form-fields-list__item" v-if="formError" v-cloak>
                      <div class="form-error">@{{ formError }}</div>
                    </li>

                    <li class="form-fields-list__item form-field-list__item_content_submit">
                      <button type="submit" class="button button_viewtype_modal c-primary-button" v-bind:class="isLoading ? 'is-load': ''">
                        <span class="button__text">
                          <?= __('Request the withdrawal'); ?>
                        </span>
                      </button>
                    </li>

                  </ul>
                </fieldset>
              </form>
            </div>

            <div class="content-column content-column_layout_b">
              @include('/loyalty/_withdraw-conditions', ['action' => true])
            </div>
          </div>
        </div>

        <button type="button" class="back-button box__back" aria-label="<?= __('Back'); ?>" @click="isWithdrawShow = !isWithdrawShow">
          <svg class="back-button__icon">
            <use xlink:href="#back-arrow"></use>
          </svg>
        </button>
      </div>
    </div>
  </referrer-balance>
<?php
} else {
        ?>

<?php
    } ?>
