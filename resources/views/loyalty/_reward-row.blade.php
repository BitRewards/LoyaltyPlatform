<?php
/**
 * @var \App\DTO\PartnerPage\RewardData
 * @var $partner                        App\Models\Partner
 * @var \App\DTO\PartnerPageData        $partnerPage
 */
$descriptionModalClass = "js-reward-description-modal-{$reward->id}";

$specialTypes = [
    App\Models\Reward::TYPE_BITREWARDS_PAYOUT => 'bitrewards-payout',
];

if ($reward->isSpecialType()) {
    ?>
@include('loyalty/rewards-special/' . $reward->getSpecialTypeTemplateName())
<?php
} elseif (!$reward->isFiatWithdrawType()) {
        ?>

<li class="offers-list__item js-updatable">
  <button
    class="offer js-offer c-offer c-text
    <?= $partnerPage->user && $reward->viewData->rewardAmount <= $partnerPage->user->balanceAmount && !$partnerPage->partner->isMigoff ? 'js-get-reward' : 'js-show-modal'; ?>"
    data-modal="<?= !$partnerPage->user ? '.js-auth-modal' : '.'.$descriptionModalClass; ?>"
    data-confirm="<?= __('Are you sure? Your account will be charged %s', htmlspecialchars($reward->viewData->reward)); ?>"
    data-url="<?=$reward->viewData->clientRewardAcquireUrl; ?>"
    type="button"
  >
    <h6 class="offer__title is-bold <?=($reward->viewData->isBigRewardValue ? 'is-smaller' : ''); ?> <?= $partnerPage->partner->isBazelevsPartner ? 'offer__title_viewtype_elki' : ''; ?>">
      <?=$reward->viewData->rewardValue; ?>
    </h6>
    <div class="offer__price">
      <?php if ($reward->shortDescription) {
            ?>
          <?=$reward->shortDescription; ?>
      <?php
        } elseif ($reward->isGiftdDiscountType()) {
            ?>
        <?php if ($reward->viewData->minimalRewardAmount) {
                ?>
          <?=$reward->viewData->minimalRewardMessage; ?>
        <?php
            } else {
                ?>
          <?=$reward->viewData->rewardMessage; ?>
        <?php
            } ?>
      <?php
        } else {
            ?>

      <?php
        } ?>
    </div>

    @if  (!$partnerPage->user || $reward->viewData->rewardAmount <= $partnerPage->user->balanceAmount)
      <div
        class="offer__status offer__status_viewtype_get"
      >
        <span class="offer__text offer__text_viewtype_price">
          <svg class="offer__text-icon">
            <use xlink:href="#tag"></use>
          </svg>
          <?= $partnerPage->partner->isGradedPercentRewardModeEnabled ? __('Get a discount') : $reward->viewData->reward; ?>
        </span>
        <span class="offer__text offer__text_viewtype_get">
          <?= __('Redeem'); ?>
        </span>
      </div>
    @else

      <div class="offer__status">
        <?php if (!$partnerPage->partner->isGradedPercentRewardModeEnabled): ?>
          <span class="offer__text offer__text_viewtype_price">
            <svg class="offer__text-icon">
              <use xlink:href="#tag"></use>
            </svg>
            <?= $partnerPage->partner->isGradedPercentRewardModeEnabled ? __('Get a discount') : $reward->viewData->reward; ?>
          </span>
        <?php endif; ?>
          <?php if (!$partnerPage->partner->isGradedPercentRewardModeEnabled): ?>
            <span class="offer__text offer__text_viewtype_get">
              <?= $partnerPage->partner->isBitRewardEnabled
                      ? __('earn %count% more BIT', $reward->viewData->pointsLeft)
                      : (
                      $reward->isFiatPriceType() || ($partnerPage->partner->isMergeBalancesEnabled && $partnerPage->partner->isFiatReferrerEnabled)
                          ? __('earn %amount% more', $reward->viewData->fiatLeft)
                          : __('earn %count% more {point|points}', $reward->viewData->pointsLeft)
                      ); ?>
            </span>
          <?php else : ?>
            <?= __('Perform %count% more {action|actions}', $reward->viewData->pointsLeft / 100); ?>
          <?php endif; ?>
        <span class="offer__timeline js-offer-timeline c-primary-bg" data-percent="<?=$reward->viewData->progressInPercent; ?>"></span>
      </div>
    @endif
  </button>
</li>

@if ($partnerPage->user)
  @push('modals')
  <div class="modal <?= $descriptionModalClass; ?>">
    <h4 class="modal__title">
      <?=$reward->title; ?>
    </h4>
    <div class="modal__body">
      <p class="modal__text i i_content_brand i_content_bold">
        <?php if ($partnerPage->partner->isMigoff): ?>
          <?= __('Please contact our manager to redeem points'); ?>
        <?php else: ?>
          <?=$reward->viewData->rewardDiscountMessage; ?>

        <?php endif; ?>
      </p>
    </div>
    <svg class="modal__close js-close-modal">
      <use xlink:href="#popup-close"></use>
    </svg>
  </div>
  @endpush
@endif

<?php
    } ?>
