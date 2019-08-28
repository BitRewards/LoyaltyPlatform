<?php
/**
 * @var \App\Models\Action $action
 * @var \App\Models\Partner $partner
 * @var \App\DTO\PartnerPageData $partnerPage
 */
?>
<?php if ($partnerPage->user && !empty($partnerPage->partner->brwAmount)) { ?>
<li class="offers-list__item js-updatable" data-block-id="brw-payout-reward-row">
  <button class="offer js-offer c-offer c-text js-go-button" type="button" data-id="bitrewards">
    <h6 class="offer__title is-bold ">
      <?= $partnerPage->partner->brwAmount ?> BIT
    </h6>
    <div class="offer__price">
      <?= __("BitRewards Payout") ?>
    </div>
    <div class="offer__status offer__status_viewtype_get c-primary-button">
        <span class="offer__text offer__text_viewtype_price">
          <svg class="offer__text-icon">
            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#tag"></use>
          </svg>
        <?= $partnerPage->user ? $partnerPage->user->getBalance() : __('Entire Balance') ?>
        </span>
      <span class="offer__text offer__text_viewtype_get">
          <?= __("Redeem") ?>
      </span>
    </div>
  </button>
</li>
<?php } ?>
