<?php
/**
 * @var App\Models\Partner
 * @var \App\DTO\PartnerPageData $partnerPage
 */
?>

<earn inline-template>
  <div class="tab-content is-hide js-tab-content" data-id="earn" data-after-show-callback="EARN_SHOW">
    <div class="tab-content__in tab-content__in_viewtype_mobile-wide tab-content__in_content_collect-points">
      <?php if ($partnerPage->partner->isOborotPromoPartner) {
    ?>
        <tip aria="<?= __('Close'); ?>">
          <template slot="text">
            <?= __('Лояльные покупатели зарабатывают баллы только за важные для вашего бизнеса действия: собственные фактические покупки, за покупки друзей, за подписки и размещения постов в социальных сетях. Размер награды в процентах или в баллах вы настраиваете по своему усмотрению.'); ?>
          </template>
        </tip>
      <?php
} ?>
      <div class="tab-content__header i i_content_mobile-hide">
        <h2 class="tab-content__title <?= $partnerPage->partner->isBazelevsPartner ? 'tab-content__title_viewtype_elki' : 'tab-content__title_viewtype_small'; ?> c-text">
          <?= $partnerPage->viewData->earnMessage; ?>
        </h2>
        <?php if ($partnerPage->partner->isBazelevsPartner) {
        ?>
          <span class="tab-content__subtitle" style="text-align:left">
            Зарабатывайте баллы за&nbsp;простые действия и&nbsp;тратьте их&nbsp;в&nbsp;разделе &laquo;Потратить баллы&raquo;
          </span>
        <?php
    } ?>
      </div>
      <div class="tab-content__incut">
        <p class="tab-content__incut-text">
          <?= $partnerPage->partner->isBitRewardEnabled ?
                __('You may spend the BIT tokens in the "%s" section', '<a class="link link_viewtype_spent-points js-go-button" data-id="spend">'.__('Spend BIT').'</a>') :
                __('Earned points can be redeemed in the  «%s» section', '<a class="link link_viewtype_spent-points js-go-button" data-id="spend">'.__('Spend points').'</a>');
            ?>
        </p>
      </div>
      <div class="tab-content__body tab-content__body_viewtype_fixed">
        <div class="scroller">
          <div class="scroller__in">
            <?php if ($partnerPage->viewData->earnTipMessage): ?>
              <div class="tip c-incut">
                <p class="tip__text">
                    <?= $partnerPage->viewData->earnTipMessage; ?>
                </p>
                <button type="button" class="tip__close js-tip-close" aria-label="<?= __('Close'); ?>">
                  <svg class="tip__close-icon" aria-hidden="true">
                    <use xlink:href="#popup-close"></use>
                  </svg>
                </button>
              </div>
            <?php endif; ?>

            <ul class="achievement-list ">


              @push('activate-plastic')
              <?php if (!$partnerPage->viewData->isActivatePlasticHidden): ?>
                <li class="achievement-list__item">
                  <button class="achievement c-achievement c-text js-show-modal" data-modal="<?= !Auth::check() ? '.js-auth-modal' : '.js-add-card-modal'; ?>" type="button">
                    <div class="achievement__thumbnail c-primary-bg">
                      <ins class="achievement__icon icon icon_content_loyalty-card"></ins>
                    </div>
                    <h6 class="achievement__title">
                        <?=$partnerPage->viewData->discountInsteadOfLoyaltyMessage; ?>
                    </h6>
                    <div class="achievement__status">
                      <span class="achievement__bonus"><?=$partnerPage->viewData->rewardNAmountMessage; ?></span>
                    </div>
                  </button>
                </li>
              <?php endif; ?>
              @endpush

              <?php if ($partnerPage->viewData->activatePlasticBeforeOtherActions) {
                ?>
                @stack('activate-plastic')
              <?php
            } ?>

              @foreach ($partnerPage->actions as $action)
                @if (!in_array($action->type, $partnerPage->partner->hiddenActions))
                  @include('/loyalty/_action-row')
                @endif
              @endforeach

              <?php if (!$partnerPage->partner->isBazelevsPartner) {
                ?>

                <?php if (!$partnerPage->viewData->activatePlasticBeforeOtherActions) {
                    ?>
                  @stack('activate-plastic')
                <?php
                } ?>
              <?php
            } ?>

          <?php if (!$partnerPage->partner->isBazelevsPartner && !$partnerPage->viewData->isEnterPromocodeHidden) {
                ?>
            <li class="achievement-list__item">
              <button class="achievement c-achievement c-text js-show-modal" data-modal="<?= !$partnerPage->user ? '.js-auth-modal' : '.js-promocode-modal'; ?>" type="button">
                <div class="achievement__thumbnail c-primary-bg">
                  <ins class="achievement__icon icon icon_content_promocode"></ins>
                </div>
                <h6 class="achievement__title">
                  <?= __('Enter the promo code'); ?>
                </h6>
                <div class="achievement__status">
                  <span class="achievement__bonus"><?= $partnerPage->viewData->rewardNAmountMessage; ?></span>
                </div>
              </button>
            </li>
          <?php
            } ?>

            </ul>
            <div class="scroller__bar-wrapper">
              <div class="scroller__bar"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</earn>
