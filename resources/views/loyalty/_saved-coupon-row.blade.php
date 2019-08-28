<?php
/**
 * @var \App\DTO\PartnerPage\SavedCouponData $coupon
 */
?>

<li class="list__item js-saved-coupon-show-usage-modal hotfix-reward-history-row"
    data-hover-title="{{ $coupon->canBeRedeemed ? __('Click here to use the discount') : '' }}"
    data-redeem-url="{{ $coupon->canBeRedeemed ? $coupon->redeemUrl : "" }}">
    <div class="operation c-text">
        <div class="operation__thumbnail operation__thumbnail_viewtype_get-discount">
            <svg class="operation__icon operation__icon_content_get-discount c-primary-fill ">
                <use xlink:href="#discount"></use>
            </svg>
        </div>

        <div class="operation__description">
            <span class="operation__meta">{{ $coupon->created }}</span>
            <div class="operation__title js-title">{{ $coupon->discountDescription }}</div>
        </div>

        <div class="operation__action">
            <button type="button"
                    <?= $coupon->canBeRedeemed ? '' : 'disabled' ?>
                    <?= $coupon->canBeRedeemed ? '' : 'style="opacity: 0.5;"' ?>
                    class="operation__status operation__status_viewtype_get-discount c-primary-button">
                {{ $coupon->statusStr }}
            </button>
        </div>
    </div>
</li>
