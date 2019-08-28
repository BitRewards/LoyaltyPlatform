<div class="reward-empty">
  <svg class="reward-empty__icon">
    <use xlink:href="#discount"></use>
  </svg>
  <span class="reward-empty__text">
    <?= $partnerPage->partner->isGradedPercentRewardModeEnabled ? __('There are currently no discounts') : __('There are currently no rewards'); ?>
  </span>
</div>