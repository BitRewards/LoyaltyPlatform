<?php
/**
 * @var \App\DTO\PartnerPage\ActionData
 * @var App\Models\Partner              $partner
 * @var \App\DTO\PartnerPageData        $partnerPage
 */
$descriptionModalClass = "js-action-description-modal-{$action->id}";

?>
<li class="achievement-list__item js-updatable" data-block-id="action-list-{{$action->id}}">
  <button class="achievement <?=!($action->viewData->canBeDone ?? 1) ? 'achievement_viewtype_success' : ''; ?> c-achievement c-text js-show-modal" data-modal="<?=($partnerPage->user ? '.'.$descriptionModalClass : '.js-auth-modal'); ?>" type="button">
    <div class="achievement__thumbnail c-primary-bg">
      <ins class="achievement__icon icon icon_content_<?=$action->viewData->iconClass; ?>"></ins>
    </div>
    <h6 class="achievement__title">
      <?=$action->viewData->title; ?>
    </h6>
    <?php if ($action->viewData->canBeDone ?? 1) {
    ?>
    <div class="achievement__status">
      <span class="achievement__bonus is-bold">
         <?= $action->viewData->rewardAmount; ?>
      </span>
    </div>
    <?php
} else {
        ?>
    <div class="achievement__status achievement__status_viewtype_success c-achievement-status">
                <span class="achievement__success-text c-primary-color">
                  <?= $action->viewData->impossibleReason; ?>
                  <svg class="achievement__success-icon">
                    <use xlink:href="#ok"></use>
                  </svg>
                </span>
    </div>
    <?php
    } ?>
  </button>

</li>

@if ($partnerPage->user && ($action->viewData->canBeDone ?? 1))
  @push('modals')
  <div class="modal <?= $descriptionModalClass; ?>
    <?= $action->isShareInstagramType() || $action->isSubscribeTelegramType() || $action->isCustomSocialActionType() ?
    'modal_viewtype_wide' : ''; ?>">
    <h4 class="modal__title">
      <?= $action->viewData->title; ?>
    </h4>
    <div class="modal__body">
      <p class="modal__text i i_content_brand i_content_bold">
        <?php if ($action->viewData->description) {
        ?>
          <?=$action->viewData->description; ?>.
          <br><br>
        <?php
    } ?>
        <?php if ($action->viewData->valuePolicyRules) {
        ?>
          <?php if ($partnerPage->partner->isAvtocodPartner) {
            ?>
            Комиссия в зависимости от стоимости заказа:
          <?php
        } else {
            ?>
            <?= __('Cashback value according to order value:'); ?>
          <?php
        } ?>
          <br />
          <?php
            foreach ($action->viewData->valuePolicyRules as $rule) {
                ?>
            <span class="i__text"><?= $rule->amountString; ?></span> <?= ($rule->orderConstraintString ? ' '.__('for orders').' '.'<span class="i__text">'.$rule->orderConstraintString.'</span>' : ''); ?><br />
            <?php
            } ?>
        <?php
    } else {
        ?>
          <?= __('You will get'); ?> <span class="i__text"><?=$action->viewData->rewardAmount; ?></span>
        <?php
    }?>
      </p>
    </div>
    @if ($action->isOrderReferralType())
      <div class="modal__footer">
          <button class="button button_viewtype_modal c-primary-button js-go-button" data-id="invite"><?= __('Invite friends'); ?></button>
      </div>
    @endif

    @if ($action->isOrderCashBackType() && $partnerPage->partner->url)
      <div class="modal__footer">
          <a class="button button_viewtype_modal c-primary-button" target="_blank" href="<?=$partnerPage->partner->url; ?>"><?= __('Make a purchase'); ?></a>
      </div>
    @endif

    @if ($action->isJoinVKType())
      <br>
      <div class="js-join-vk" data-event-url="<?=$action->viewData->clientEventProcessUrl; ?>" data-group-id="<?=$action->viewData->groupId; ?>"></div>
    @endif

    @if ($action->isJoinFBType())
      <br>
      <div class="js-join-fb" data-event-url="<?=$action->viewData->clientEventProcessUrl; ?>" data-page-url="<?=$action->viewData->pageUrl; ?>"></div>
    @endif

    @if ($action->isShareFBType())
      <div class="modal__footer">
        <button class="button button_viewtype_modal c-primary-button js-action-share-fb" data-event-url="<?=$action->viewData->clientEventProcessUrl; ?>" data-share-url="<?=$action->viewData->shareUrl; ?>">
          <?= __('Share on Facebook'); ?>
        </button>
      </div>
    @endif

    @if ($action->isShareVKType())
      <div class="modal__footer">
        <button class="button button_viewtype_modal c-primary-button js-action-share-vk" data-event-url="<?=$action->viewData->clientEventProcessUrl; ?>" data-share-url="<?=$action->viewData->shareUrl; ?>">
          <?= __('Share on VK'); ?>
        </button>
      </div>
    @endif

    @if ($action->isShareInstagramType())
        <div class="modal__footer">
          <button class="button button_viewtype_modal c-primary-button js-show-modal" data-modal=".js-instagram-post-modal">
            <?= __('Completed'); ?>
          </button>
        </div>
    @endif

    @if ($action->isSubscribeTelegramType())
      <div class="modal__footer">
        <button class="button button_viewtype_modal c-primary-button js-show-modal" data-modal=".js-subscribe-telegram-modal">
          <?= __('Completed'); ?>
        </button>
      </div>
    @endif

    @if ($action->isCustomSocialActionType())
      <div class="modal__footer">
        <button class="button button_viewtype_modal c-primary-button js-show-modal" data-modal=".js-custom-social-action-modal">
          <?= __('Completed'); ?>
        </button>
      </div>
    @endif

    <svg class="modal__close js-close-modal">
      <use xlink:href="#popup-close"></use>
    </svg>
  </div>

  @endpush
@endif
