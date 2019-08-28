<?php
/**
 * @var App\Models\Partner
 * @var App\DTO\PartnerPageData $partnerPage
 */
?>

<div class="tab-content tab-content_deposit is-hide js-tab-content" data-id="deposit">
  <div class="tab-content__in tab-content__in_viewtype_mobile-wide">
    <div class="tab-content__body tab-content__body_viewtype_fixed">
      <div class="scroller">
        <div class="scroller__in">
          <h2 class="tab-content__title tab-content__title_viewtype_centered c-text">
            <?=__('Where do you want to deposit BIT from?'); ?>
          </h2>

          <div class="content-columns content-columns_content_deposit-action">
            <div class="content-column content-column_layout_a">
              <button type="button" class="button button_content_deposit c-deposit-button js-show-modal" data-modal=".js-deposit-personal-modal">
                <svg class="button__icon button__icon_content_wallet">
                  <use xlink:href="#wallet"></use>
                </svg>
                <span class="button__text">
                  <?=__('From my<br> Ethereum wallet'); ?>
                </span>
              </button>
            </div>

            <div class="content-column content-column_layout_b">
              <button type="button" class="button button_content_deposit c-deposit-button js-show-modal" data-modal=".js-deposit-shop-modal">
                <svg class="button__icon button__icon_content_shop">
                  <use xlink:href="#shop"></use>
                </svg>
                <span class="button__text">
                  <?=__('From another BitRewards store'); ?>
                </span>
              </button>
            </div>

            <div class="content-column content-column_layout_c">
              <button type="button" class="button button_content_deposit c-deposit-button js-show-modal" data-modal=".js-exchange-modal">
                <svg class="button__icon button__icon_content_exchange">
                  <use xlink:href="#exchange"></use>
                </svg>
                <span class="button__text">
                  <?=__('Exchange<br>ETH to BIT'); ?>
                </span>
              </button>
            </div>
          </div>

          <div class="transaction">
            <h3 class="transaction__title">
              <?=__('Your deposit transactions'); ?>
            </h3>

            <div class="transaction__body">
              <div class="transaction__body js-deposit-transactions-list">
                @include('loyalty/_bitrewards-deposit-transactions', ['transactions' => $partnerPage->depositTransactions])
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <button class="back-button js-go-button" type="button" aria-label="<?= __('Back'); ?>" data-id="balance">
    <svg class="back-button__icon">
      <use xlink:href="#back-arrow"></use>
    </svg>
  </button>
</div>
