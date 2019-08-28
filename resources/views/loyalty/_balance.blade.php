<?php
/**
 * @var \App\DTO\PartnerPageData
 */
?>
<?php if ($partnerPage->user) {
    ?>
<Balance inline-template>
  <div class="tab-content is-hide js-tab-content js-updatable" data-id="balance" data-tab-close-callback="BALANCE_CLOSE">
    <div class="scroller scroller_viewtype_top">
      <div class="scroller__in">
        <div class="tab-content__in tab-content__in_content_balance">
          <?php if ($partnerPage->partner->isOborotPromoPartner) {
        ?>
            <tip aria="<?= __('Close'); ?>">
              <template slot="text">
                <?= __('Здесь отображается баланс пользователя в баллах и все купоны, который пользователь может сохранить из любого инструмента BitRewards, установленного на вашем сайте.'); ?>
              </template>
            </tip>
          <?php
    } ?>
        <div class="tab-content__header">
          <?php if (!$partnerPage->partner->isGradedPercentRewardModeEnabled): ?>
            <h2 class="tab-content__title tab-content__title_viewtype_centered tab-content__title_viewtype_small c-text">
              <?= __('Your balance'); ?>: <b class="js-balance"><?= (int) $partnerPage->user->balanceAmount; ?></b> <?=$partnerPage->user->currency; ?>
            </h2>
          <?php endif; ?>
          <?php if ($partnerPage->partner->isBitRewardEnabled) {
        ?>
            <p class="tab-content__text">
              <?= __('You can redeem your <strong>BIT tokens</strong> for purchases or transfer it to your Ethereum wallet.<br><br> Also you can transfer more BIT tokens to this wallet from external Ethereum wallet or another BitRewards store.'); ?>
            </p>
            <ul class="tab-content__action">
              <li class="tab-content__action-item">
                <button class="button button_type_block button_viewtype_primary c-primary-button js-go-button" data-id="withdraw">
                  <span class="button__text">
                    <?= __('Withdraw BIT'); ?>
                  </span>
                </button>
              </li>

              <li class="tab-content__action-item">
                <button class="button button_type_block button_viewtype_primary c-primary-button js-go-button" data-id="deposit">
                  <span class="button__text">
                    <?= __('Deposit BIT'); ?>
                  </span>
                </button>
              </li>
            </ul>
          <?php
    } ?>
        </div>
        <div class="tab-content__body">
          <h4 class="heading heading_level_4 heading_viewtype_center heading_content_rewards"><?= $partnerPage->partner->isGradedPercentRewardModeEnabled ? __('Your discounts') : __('Your rewards'); ?>:</h4>
          @if (empty($partnerPage->couponList))
            @include('loyalty/_empty-reward')
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
          </div>
        </div>
      </div>
    </div>
  </div>
</Balance>
<?php
} else {
        ?>
<?php
    } ?>
