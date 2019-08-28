<?php
/**
 * @var \App\DTO\PartnerPageData
 */
?>

<?php if ($partnerPage->user) {
    ?>
<div class="tab-content is-hide js-tab-content" data-id="cabinet">
  <div class="tab-content__in tab-content__in_content_history">
    <div class="scroller scroller_viewtype_top">
      <div class="scroller__in">
        <div class="tab-content__header">
          <div class="person person_viewtype_cabinet">
            <div class="person__avatar">
              <img src="/loyalty/images/content/avatar.png" width="120" height="120">
            </div>
            <?= __('You logged in as'); ?>
            <div class="person__name"><?= Auth::user()->getTitle(); ?></div>
            <div class="person__action">
              <?php if (!$partnerPage->viewData->isEditProfileButtonHidden): ?>
                <button type="button" class="button button_viewtype_mono js-go-button" data-id="profile">
                  <svg class="button__icon button__icon_content_profile" aria-hidden="true">
                    <use xlink:href="#profile"></use>
                  </svg>
                  <?= __('edit profile'); ?>
                </button>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <div class="tab-content__body">
          <?php if ($partnerPage->user->codes) {
        ?>
          <ul class="list list_content_cards js-updatable" data-block-id="codes-list">
            <?php foreach ($partnerPage->user->codes as $code) {
            ?>
            <li class="list__item">
              <div class="operation operation_viewtype_cabinet c-text">
                <div class="operation__thumbnail c-primary-bg">
                  <ins class="operation__icon icon icon_content_card"></ins>
                </div>
                <div class="operation__description">
                  <span class="operation__meta">
                    <?=$code->acquiredAtStr; ?>
                  </span>
                  <div class="operation__title js-title">
                    <?= __('Loyalty Card'); ?> <b><?=$code->loyaltyCard; ?></b>
                  </div>
                </div>
                <div class="operation__status operation__status_viewtype_checked c-primary-color c-primary-status">
                  <?= __('Received'); ?>
                </div>
              </div>
            </li>
            <?php
        } ?>
          </ul>
          <?php
    } ?>

          <?php if (!$partnerPage->partner->isFiatReferrerEnabled) {
        ?>
            <div class="add-card-action">
              <button type="button" class="button button_viewtype_modal c-primary-button button_content_add-card js-show-modal" data-modal=".js-add-card-modal">
                <?= __('Add loyalty card'); ?>
              </button>
            </div>
          <?php
    } ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
} else {
        ?>

<?php
    } ?>
