<?php
/**
 * @var FrontendController
 * @var \App\DTO\PartnerPageData $partnerPage
 */
?>

<spend inline-template>
  <div class="tab-content is-hide js-tab-content" data-id="spend" data-after-show-callback="OFFER_ANIMATEON" data-after-hide-callback="OFFER_ANIMATEOFF">
    <div class="tab-content__in tab-content__in_viewtype_mobile-wide tab-content__in_content_spend">
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
          <?= $partnerPage->viewData->spendMessage; ?>
        </h2>
        <?php if ($partnerPage->partner->isBazelevsPartner) {
        ?>
          <span class="tab-content__subtitle" style="text-align:left">
            Сотни билетов в кино, плакаты с автографами артистов, подарки от партнеров фильма, неожиданные сюрпризы и даже 100 авиабилетов на любое направление авиакомпании S7 Airlines. Начинайте копить баллы и уже скоро тут появятся возможности их потратить!
          </span>
        <?php
    } ?>
      </div>
      <div class="tab-content__body tab-content__body_viewtype_fixed js-updatable is-tip-show js-tip-container" data-block-id="rewards-list">
        <div class="scroller">
          <div class="scroller__in">
            <?php if ($partnerPage->viewData->spendTipMessage): ?>
            <div class="tip c-incut">
              <p class="tip__text">
                  <?= $partnerPage->viewData->spendTipMessage; ?>
              </p>
              <button type="button" class="tip__close js-tip-close" aria-label="<?= __('Close'); ?>">
                <svg class="tip__close-icon" aria-hidden="true">
                  <use xlink:href="#popup-close"></use>
                </svg>
              </button>
            </div>
          <?php endif; ?>

            <ul class="offers-list js-offers">
              @foreach ($partnerPage->rewards as $reward)
                @include('loyalty/_reward-row')
              @endforeach
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</spend>
