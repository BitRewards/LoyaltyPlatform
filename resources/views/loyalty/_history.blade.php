<?php
/**
 * @var FrontendController
 * @var \App\DTO\PartnerPageData $partnerPage
 */
?>

<History inline-template>
  <div class="tab-content is-hide js-tab-content" data-id="history">
    <div class="tab-content__in tab-content__in_content_history <?= $partnerPage->partner->isOborotPromoPartner ? 'is-tip-show js-tip-container' : ''; ?>">
      <?php if ($partnerPage->partner->isOborotPromoPartner) {
    ?>
        <tip
          aria="<?= __('Close'); ?>"
          category="Onboarding"
          hover="onboardingHistoryHover"
          close="onboardingHistoryCloseClick"
        >
          <template slot="text">
            <?= __('In the “History” section a loyal customer sees all the facts of earning and redeeming points.'); ?>
          </template>
        </tip>
      <?php
} ?>
      <div class="tab-content__header i i_content_mobile-hide">
        <h2 class="tab-content__title tab-content__title_viewtype_small c-text">
          <?= __('Action history'); ?>
        </h2>
      </div>
      <div class="tab-content__body tab-content__body_viewtype_fixed">
        <div class="scroller">
          <div class="scroller__in">
            <ul class="history-list list js-transactions js-updatable" data-block-id="transactions-list">
              @if ($partnerPage->user)
                @include('loyalty/_transactions')
              @else
                @include('loyalty/_guest-transactions')
              @endif

              <?php if (false) {
        ?>
                <li class="list__item">
                  <div class="operation c-text">
                    <div class="operation__thumbnail operation__thumbnail_transparent">
                      <svg class="operation__icon icon icon_content_svg c-primary-fill">
                        <use xlink:href="#burn"></use>
                      </svg>
                    </div>

                    <div class="operation__description">
                      <span class="operation__meta">
                        today, 1:38 am
                      </span>
                      <div class="operation__title js-title">
                        Burn points
                      </div>
                    </div>

                    <div class="operation__price ">
                      -1500 BIT
                    </div>

                    <div class="operation__status operation__status_viewtype_accepted c-primary-status js-tooltip" data-tooltip-text="Approved">
                      Approved
                    </div>
                  </div>
                </li>

                <li class="list__item">
                  <div class="operation c-text">
                    <div class="operation__thumbnail operation__thumbnail_viewtype_burned c-primary-bg">
                     <ins class="operation__icon icon icon_content_visit"></ins>
                    </div>

                    <div class="operation__description">
                      <span class="operation__meta">
                        today, 1:38 am
                        <span class="operation__meta operation__meta_viewtype_expired c-meta">
                          Expires 12.12.2018
                        </span>
                      </span>
                      <div class="operation__title js-title">
                        Daily visit
                      </div>
                    </div>

                    <div class="operation__price ">
                      +50 BIT
                    </div>

                    <div class="operation__status operation__status_viewtype_burned js-tooltip" data-tooltip-text="Burned">
                      Burned
                    </div>
                  </div>
                </li>

                <li class="list__item">
                  <div class="operation c-text">
                    <div class="operation__thumbnail operation__thumbnail_transparent">
                      <svg class="operation__icon icon icon_content_svg c-primary-fill">
                        <use xlink:href="#lost"></use>
                      </svg>
                    </div>

                    <div class="operation__description">
                      <span class="operation__meta">
                        today, 1:38 am
                        <span class="operation__meta operation__meta_viewtype_expired c-meta">
                          Expires 12.12.2018
                        </span>
                      </span>
                      <div class="operation__title js-title">
                        Упущенная возможность получения <strong>"Скидка 5%"</strong>
                      </div>
                    </div>
                  </div>
                </li>

                <li class="list__item">
                  <div class="operation c-text">
                    <div class="operation__thumbnail operation__thumbnail_transparent">
                      <svg class="operation__icon icon icon_content_svg c-primary-fill">
                        <use xlink:href="#timeout"></use>
                      </svg>
                    </div>

                    <div class="operation__description">
                      <span class="operation__meta">
                        today, 1:38 am
                      </span>
                      <div class="operation__title js-title">
                        Закончился срок действия <strong>"Скидка 20%"</strong>
                      </div>
                    </div>
                  </div>
                </li>

                <li class="list__item">
                  <div class="operation c-text">
                    <div class="operation__thumbnail operation__thumbnail_transparent">
                      <svg class="operation__icon icon icon_content_svg c-primary-fill">
                        <use xlink:href="#save"></use>
                      </svg>
                    </div>

                    <div class="operation__description">
                      <span class="operation__meta">
                        today, 1:38 am
                      </span>
                      <div class="operation__title js-title">
                        Сохранена скидка <strong>"Скидка 20%"</strong>
                      </div>
                    </div>
                  </div>
                </li>

                <li class="list__item">
                  <div class="operation c-text">
                    <div class="operation__thumbnail operation__thumbnail_transparent">
                      <svg class="operation__icon icon icon_content_svg c-primary-fill">
                        <use xlink:href="#buy"></use>
                      </svg>
                    </div>

                    <div class="operation__description">
                      <span class="operation__meta">
                        today, 1:38 am
                      </span>
                      <div class="operation__title js-title">
                        Приобретена <strong>"Скидка 5%"</strong>
                      </div>
                    </div>

                    <div class="operation__price ">
                      -1500 BIT
                    </div>

                    <div class="operation__status operation__status_viewtype_accepted c-primary-color c-primary-status js-tooltip" data-tooltip-text="Approved">
                      Approved
                    </div>
                  </div>
                </li>
              <?php
    } ?>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</History>
