<?php
/**
 * @var App\Models\Partner
 * @var $overriddenTitle
 * @var \App\DTO\PartnerPageData $partnerPage
 */
?>
@extends('loyalty/layouts/main')

@section('content')
<div id="app">
  <div class="overlay js-overlay">
    <div class="overlay__content">
      <div class="popup loyalty-popup <?= $partnerPage->partner->isBitRewardEnabled ? 'loyalty-popup_bit' : ''; ?> js-loyalty-popup">
        <div class="loyalty-popup__header js-loyalty-popup-header">
          <h1 class="loyalty-popup__title i i_content_bold c-text">
              <?=$partnerPage->viewData->cabinetTitle; ?>
          </h1>
          <span class="loyalty-popup__tab-title js-tab-title"></span>
          <?php if (!$partnerPage->viewData->isPopupCloseButtonHidden): ?>
            <svg class="loyalty-popup__close js-close-popup" style="display: none;">
              <use xlink:href="#popup-close"></use>
            </svg>
          <?php else: ?>
            <span class="js-close-popup" style="display: none;"></span>
          <?php endif; ?>
          <div class="loyalty-popup__menu-opener js-menu-opener has-alert">
            <svg class="loyalty-popup__menu-opener-icon">
              <use xlink:href="#menu"></use>
            </svg>
          </div>
          <button class="back-button js-go-button" data-id="login">
            <svg class="back-button__icon">
              <use xlink:href="#back-arrow"></use>
            </svg>
            Назад
          </button>
          <div class="loyalty-popup__menu-close js-menu-close">
            <svg class="loyalty-popup__menu-opener-icon">
              <use xlink:href="#menu"></use>
            </svg>
          </div>
        </div>
        <div class="loyalty-popup__body">
          <div class="content-columns content-columns_content_loyalty">
            <div class="content-column content-column_layout_a">
              <div class="content-column__in">
                @include('/loyalty/_menu')
              </div>
            </div>
            <div class="content-column content-column_layout_b">
              <div class="content-column__in">
                @include('/loyalty/_login')
                @include('/loyalty/_email-not-provided')
                @include('/loyalty/_phone-not-provided')
                @include('/loyalty/_create-password')
                @include('/loyalty/_enter-password')
                @include('/loyalty/_email-not-confirmed')
                @include('/loyalty/_phone-not-confirmed')
                @include('/loyalty/_reset-password-confirm-phone')
                @include('/loyalty/_reset-password')
                @include('/loyalty/_reset-password-sent')
                @include('/loyalty/_confirm-merge-by-phone')
                @include('/loyalty/_confirm-merge-by-email')
                @include('/loyalty/_confirmation-code-email')
                @include('/loyalty/_confirmation-code-phone')
                @include('/loyalty/_enter-email')
                @include('/loyalty/_enter-phone')
                <?php if ($partnerPage->partner->isBitRewardEnabled) {
    ?>
                  @include('/loyalty/_withdraw')
                  @include('/loyalty/_deposit')
                <?php
}?>
                <?php if ($partnerPage->partner->isFiatReferrerEnabled) {
        ?>
                  @include('/loyalty/_dashboard')
                <?php
    }?>
                @include('/loyalty/_earn')
                @include('/loyalty/_spend')
                @include('/loyalty/_invite')
                @include('/loyalty/_history')
                @include('/loyalty/_help')
                @include('/loyalty/_cabinet')
                @include('/loyalty/_balance')
                @include('/loyalty/_settings')
                @include('/loyalty/_profile')
                @include('/loyalty/_balance')
                @include('/loyalty/_referrer-balance')
                @include('/loyalty/_questionary')
                @include('/loyalty/_modals')
                <?php if ($partnerPage->partner->isBitRewardEnabled) {
        ?>
                  @include('/loyalty/_bitrewards')
                <?php
    } ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
