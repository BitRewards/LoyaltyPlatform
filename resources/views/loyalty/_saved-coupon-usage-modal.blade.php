<?php
/**
 * @var \App\DTO\PartnerPage\SavedCouponData $savedCoupon
 */
?>
<div class="modal modal_content_auth js-transaction-{{ $savedCoupon->id }}">
  <h4 class="modal__title">
    {{ __('Thank you!') }}
  </h4>
  <div class="modal__body">
    <p class="modal__text">
      {{ __('Your discount') }}: <b>{{ $savedCoupon->discountDescription }}</b>
    </p>
  </div>
  <div class="modal__footer">
    <a class="button button_viewtype_modal c-primary-button js-close-modal"
       href="{{ $savedCoupon->redeemUrl }}" target="_blank">
      {{ __('Use your discount') }}
    </a>
  </div>
  <svg class="modal__close js-close-modal">
    <use xlink:href="#popup-close"></use>
  </svg>
</div>
