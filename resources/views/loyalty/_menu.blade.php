<?php
/**
 * @var App\Models\Partner
 * @var \App\DTO\PartnerPageData $partnerPage
 */
?>

<div class="sidemenu c-sidemenu js-sidemenu js-menu">
  <div class="sidemenu__header">
    <div class="welcome">
      <div class="welcome__thumbnail" style="<?= !$partnerPage->user ? 'visibility: hidden;' : ''; ?>">
          <div class="upload-image js-image-upload <?= $partnerPage->user ? 'is-image-loaded' : ''; ?>" >
            <form action="upload_picture_url_would_be_here" class="js-picture-form js-ajax-form" method="POST" enctype="multipart/form-data">
              <input type="hidden" class="js-picture-url">
              <label class="upload-image__label" for="picture">
                <input type="file" name="picture" class="upload-image__input js-picture" style="display: none">
              </label>
            </form>
            <a class="avatar js-go-button" data-id="cabinet" data-close-sidemenu="1">
              <img class="avatar__image js-avatar" src="<?= $partnerPage->user->avatar ?? ''; ?>">
              <span class="avatar__remove js-avatar-remove" style="display: none">
                <svg class="avatar__remove-icon">
                  <use xlink:href="#popup-close"></use>
                </svg>
              </span>
            </a>
          </div>
      </div>
      <div class="welcome__description">

        @if ($partnerPage->viewData->isLogoutButtonHidden)
          @if ($partnerPage->user)
            <a class="person js-go-button" data-id="cabinet" data-close-sidemenu="1">
              <span class="person__name">
                {{ $partnerPage->user->getUserTitle() }}
              </span>
              <span class="person__surname">
              </span>
            </a>
          @endif
        @else
          @if ($partnerPage->user)
            <a class="person js-go-button" data-id="cabinet" data-close-sidemenu="1">
              <span class="person__name">
                {{ $partnerPage->user->getUserTitle() }}
              </span>
              <span class="person__surname">
              </span>
            </a>

            <form action="{{ $partnerPage->viewData->logoutUrl }}">
              <button class="welcome__logout" type="submit">
                  <?= __('Logout'); ?>
                <svg class="welcome__logout-icon">
                  <use xlink:href="#arrow-left"></use>
                </svg>
              </button>
            </form>
          @else
            <button class="welcome__login js-go-button" type="button" data-id="login">
                <?= __('Login'); ?>
            </button>
          @endif
        @endif
      </div>
    </div>
  </div>
  <div class="sidemenu__body">
    <ul class="menu">
      <?php if ($partnerPage->user && !$partnerPage->partner->isFiatReferrerEnabled) {
    ?>
        <?php if (!$partnerPage->partner->isGradedPercentRewardModeEnabled): ?>
          <li class="menu__item">
            <a class="sidemenu__incut c-primary-bg c-primary-top-triangle js-updatable js-menu-item" data-block-id="balance" data-id="balance" >
              <div class="sidemenu__incut-in">
                <span class="sidemenu__incut-text i i_content_points">
                  <?= __('You have'); ?>
                  <span class="i__text js-balance">
                    <?= $partnerPage->partner->isBitRewardEnabled ? $partnerPage->user->balanceAmount : (int) $partnerPage->user->balanceAmount; ?>
                  </span>
                  <?= $partnerPage->user->currency; ?>
                </span>

                <?php if ($partnerPage->partner->isBitRewardEnabled) {
        ?>
                <span class="badge">
                  = <?=$partnerPage->user->balanceInPartnerCurrency; ?>
                </span>
                <?php
    } ?>

                @if (!empty($partnerPage->bitrewardsPayoutTransactions))
                <div class="balance-alert "> <!-- is-animate to make it shake -->
                  <svg class="balance-alert__icon">
                    <use xlink:href="#pocket"></use>
                  </svg>
                  <span class="balance-alert__text">
                    {{ count($partnerPage->bitrewardsPayoutTransactions) }}
                  </span>
                </div>
                @else
                <div class="balance__arrow" aria-hidden="true"></div>
                @endif
              </div>
            </a>
          </li>
        <?php else: ?>
          <li class="menu__item">
            <a class="sidemenu__incut c-primary-bg c-primary-top-triangle js-updatable js-menu-item" data-block-id="spend" data-id="spend">
              <div class="sidemenu__incut-in">
                  <span class="sidemenu__incut-text i i_content_points">
                    <?= __('Your discount'); ?>
                    <span class="i__text js-balance">
                      <?= $partnerPage->user->balanceAmountPercent; ?>
                    </span>
                  </span>

                  <?php if ($partnerPage->partner->isBitRewardEnabled) {
        ?>
                <span class="badge">
                    = <?=$partnerPage->user->balanceInPartnerCurrency; ?>
                  </span>
                  <?php
    } ?>

                @if (!empty($partnerPage->bitrewardsPayoutTransactions))
                  <div class="balance-alert "> <!-- is-animate to make it shake -->
                    <svg class="balance-alert__icon">
                      <use xlink:href="#pocket"></use>
                    </svg>
                    <span class="balance-alert__text">
                      {{ count($partnerPage->bitrewardsPayoutTransactions) }}
                    </span>
                  </div>
                @else
                  <div class="balance__arrow" aria-hidden="true"></div>
                @endif
              </div>
            </a>
          </li>
        <?php endif; ?>
      <?php
} ?>

      <?php if ($partnerPage->referrerBalance) {
        ?>
        <li class="menu__item">
          <a class="sidemenu__incut c-primary-bg c-primary-top-triangle js-updatable js-menu-item" data-block-id="referrer-balance" data-id="referrer-balance" >
            <div class="sidemenu__incut-in">
              <span class="sidemenu__incut-text i i_content_points">
                <?= __('Your balance:'); ?>
                <div class="i__text js-balance">
                    <?= $partnerPage->referrerBalance->availableForWithdraw; ?>
                </div>
                <div class="balance__arrow" aria-hidden="true"></div>
              </span>
            </div>
          </a>
        </li>
      <?php
    } ?>

      <?php if (!$partnerPage->viewData->isEarnBitHidden) {
        ?>
      <li class="menu__item">
        <a class="menu__item-link c-menu-item js-menu-item" data-id="earn">
          <?= $partnerPage->viewData->earnMessage; ?>
          <svg class="menu__item-icon menu__item-icon_content_earn-points c-primary-menu-icon">
            <use xlink:href="#earn-points"></use>
          </svg>
        </a>
      </li>
      <?php
    } ?>

      <?php if (!$partnerPage->viewData->isSpendBitHidden) {
        ?>
      <li class="menu__item">
        <a class="menu__item-link c-menu-item js-menu-item js-updatable" data-id="spend" data-block-id="spend-menu-row">
          <?= $partnerPage->viewData->spendMessage; ?>
          <svg class="menu__item-icon menu__item-icon_content_basket c-primary-menu-icon">
            <use xlink:href="#basket"></use>
          </svg>
          @if ($partnerPage->getAvailableRewardsCount())
            <span class="menu__alert c-primary-bg">
              {{ $partnerPage->getAvailableRewardsCount() }}
            </span>
          @endif
        </a>
      </li>
      <?php
    } ?>

      <?php if ($partnerPage->partner->isFiatReferrerEnabled) {
        ?>
        <li class="menu__item">
          <a class="menu__item-link c-menu-item js-menu-item js-updatable" data-id="dashboard">
            <?= __('Referrer Dashboard'); ?>
            <svg class="menu__item-icon menu__item-icon_content_dashboard c-primary-menu-icon">
              <use xlink:href="#dashboard"></use>
            </svg>
          </a>
        </li>
      <?php
    } ?>

      <?php if ($partnerPage->partner->isOrderReferralActionExist && !$partnerPage->viewData->isInviteFriendsHidden) {
        ?>
      <li class="menu__item">
        <a class="menu__item-link c-menu-item js-menu-item" data-id="invite">
          <?= $partnerPage->partner->isFiatReferrerEnabled ? __('Referral link') : __('Invite a friend'); ?>
          <svg class="menu__item-icon menu__item-icon_content_invite-friend c-primary-menu-icon">
            <use xlink:href="#invite-friend"></use>
          </svg>
        </a>
      </li>
      <?php
    } ?>
      <li class="menu__item">
        <a class="menu__item-link c-menu-item js-menu-item" data-id="history">
          <?= __('History'); ?>
          <svg class="menu__item-icon menu__item-icon_content_history c-primary-menu-icon">
            <use xlink:href="#history"></use>
          </svg>
        </a>
      </li>
      <?php if ($partnerPage->partner->isGradedPercentRewardModeEnabled): ?>
      <li class="menu__item">
        <a class="menu__item-link c-menu-item js-menu-item js-show-modal" data-id="balance">
            <?= __('Discounts received'); ?>
          <svg class="menu__item-icon menu__item-icon_content_help c-primary-menu-icon">
            <use xlink:href="#discountReceived"></use>
          </svg>
        </a>
      </li>

      <li class="menu__item">
          <a class="menu__item-link c-menu-item js-menu-item js-show-modal" data-modal=".js-how-it-works" data-id="spend">
              <?= __('How it works'); ?>
              <svg class="menu__item-icon menu__item-icon_content_help c-primary-menu-icon">
                  <use xlink:href="#help"></use>
              </svg>
          </a>
      </li>
      <?php endif; ?>

      <?php if (false) {
        ?>
      <li class="menu__item">
        <a class="menu__item-link c-menu-item js-menu-item" data-id="help">
          <?= __('Help'); ?>
          <svg class="menu__item-icon menu__item-icon_content_help c-primary-menu-icon">
            <use xlink:href="#help"></use>
          </svg>
        </a>
      </li>
      <?php
    } ?>
    </ul>
  </div>
  <div class="sidemenu__footer">
    <a class="made-by" href="<?= $partnerPage->partner->brandUrl ?? 'https://bitrewards.com/'; ?>" target="_blank">
      <svg class="made-by__icon">
        <use xlink:href="#bitrewards"></use>
      </svg>
      <span class="made-by__text">
      <?= __('Bitrewards'); ?>
      </span>
    </a>

    <?php if ($partnerPage->partner->isBitRewardEnabled) {
        ?>
      <a class="help js-menu-item" data-id="help">
        <?= __('Help'); ?>
        <svg class="help__icon">
          <use xlink:href="#help"></use>
        </svg>
      </a>
    <?php
    } ?>
  </div>
</div>
