<?php
/**
 * @var App\Models\Partner
 * @var \App\DTO\PartnerPageData $partnerPage
 */
$referralLink = $partnerPage->user->referralLink ?? null;
?>

<?php if ($partnerPage->partner->isOrderReferralActionExist): ?>
<invite inline-template>
    <div class="tab-content is-hide js-tab-content" data-id="invite" <?= !$partnerPage->partner->isHowItWorksHidden ? 'data-after-show-callback="INVITE_SHOW"' : ''; ?>>
        <div class="tab-content__in tab-content__in_content_invite <?= $partnerPage->partner->isOborotPromoPartner ? 'is-tip-show js-tip-container' : ''; ?>">
            <div class="scroller scroller_viewtype_top">
                <div class="scroller__in">
                    <?php if ($partnerPage->partner->isOborotPromoPartner) {
    ?>
                    <tip aria="<?= __('Close'); ?>">
                      <template slot="text">
                        Мотивируйте лояльных покупателей приглашать своих друзей. Лояльные покупатели распространяют
                        реферальные ссылки, их друзья получают скидки, а лояльный покупатель&nbsp;&mdash; кэшбэк за
                        покупки друзей. Плавающий и накопительный кэшбэк настраиваются по запросу.
                      </template>
                    </tip>
                    <?php
} ?>

                    <?php if ($partnerPage->partner->isFiatReferrerEnabled) {
        ?>
                    <div class="tab-content__header tab-content__header_content_image">
                        <div class="tab-content__header-image tab-content__header-image_content_referrer c-primary-bg">
                            <svg class="tab-content__header-icon">
                                <use xlink:href="#megafon"></use>
                            </svg>
                        </div>
                        <h2 class="tab-content__title tab-content__title_content_referral tab-content__title_viewtype_centered c-text">
                            <?= $partnerPage->partner->fiatWithdrawInviteTitle; ?>
                        </h2>
                        <span class="tab-content__subtitle">
                            <?=$partnerPage->partner->clientReferralSubtitle; ?><br>
                            <?php if (!$partnerPage->viewData->isClientReferralHeadingHidden): ?>
                                    <?=$partnerPage->partner->clientReferralHeading; ?>
                            <?php endif; ?>
                        </span>
                        <?php if (!$partnerPage->partner->isHowItWorksHidden): ?>
                        <button type="button"
                                class="button button_viewtype_link button_content_how i i_content_bold js-show-modal"
                                data-modal=".js-how-it-works">
                <span class="button__text i__text c-primary-color">
                  <?= __('How it works'); ?>
                </span>
                        </button>
                        <?php endif; ?>
                    </div>

                    <div class="tab-content__body">
                        <?php if ($partnerPage->partner->isReferralLinkEnabled) {
            ?>
                        <div class="box box_content_share" style="display: none;">
                            <div class="box__in content-columns content-columns_content_share <?= !$partnerPage->partner->image ? 'no-img' : ''; ?>">
                                <div class="content-column content-column_layout_a">
                                    <form class="form form_content_share">
                                        <div class="form-fields-group">
                                            <h4 class="form-fields-group__title">
                                                <?= __('Share the link to&nbsp;this product page and earn:'); ?>
                                            </h4>
                                            <ul class="form-fields-list">
                                                <li class="form-fields-list__item">
                                                    <share-button
                                                        hover-text="<?= __('Copy link'); ?>"
                                                        success-text="<?= __('Copied!'); ?>"
                                                        clipboard-text="http://bitrewards/123abchttp://bitrewards/123abc"
                                                        event-category="Using"
                                                        event-action="copyProductUrlClick"
                                                        no-cut-link="true"
                                                    >
                                                    </share-button>
                                                </li>

                                                <li class="form-fields-list__item">
                                                    <?= __('or through the social network:'); ?>
                                                    <ul class="social-auth social-auth_content_share is-small">
                                                        <li class="social-auth__item">
                                                            <button class="social-button social-button_type_fb is-small js-action-share-fb"
                                                                    type="button"
                                                                    data-event-url="<?=$partnerPage->partner->clientEventProcessorUrl; ?>"
                                                                    data-share-url="<?= $referralLink; ?>">
                                                                <svg class="social-button__icon social-button__icon_type_fb">
                                                                    <use xlink:href="#fb"></use>
                                                                </svg>
                                                                <span class="social-button__text">
                                      <?= __('Share'); ?>
                                    </span>
                                                            </button>
                                                        </li>
                                                        <?php if (HLanguage::isRussian()): ?>
                                                        <li class="social-auth__item">
                                                            <button class="social-button social-button_type_vk is-small js-action-share-vk"
                                                                    data-event-url="<?=$partnerPage->partner->clientEventProcessorUrl; ?>"
                                                                    data-share-url="<?= $referralLink; ?>"
                                                                    type="button">
                                                                <svg class="social-button__icon social-button__icon_type_vk">
                                                                    <use xlink:href="#vk"></use>
                                                                </svg>
                                                                <span class="social-button__text">
                                        <?= __('Share'); ?>
                                      </span>
                                                            </button>
                                                        </li>
                                                        <?php endif; ?>
                                                        <li class="social-auth__item">
                                                            <button class="social-button social-button_type_email is-small js-show-modal"
                                                                    data-modal=".js-invite-modal" type="button">
                                                                <svg class="social-button__icon social-button__icon_type_email">
                                                                    <use xlink:href="#email"></use>
                                                                </svg>
                                                                <span class="social-button__text">
                                      <?= __('Email'); ?>
                                    </span>
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
                                    </form>
                                </div>

                                <div class="content-column content-column_layout_b">
                                    <div class="story story_content_share">
                                        <div class="story__thumbnail">
                                            <img class="story__image"
                                                 src="https://images.ua.prom.st/964898585_w200_h200_117293_36192302.jpg">
                                        </div>

                                        <div class="story__title">
                                            Кроссовки Nike
                                        </div>
                                        <div class="story__meta">
                                            Цена: 10 000₽
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box box_content_share">
                            <div class="box__in content-columns content-columns_content_share <?= !$partnerPage->partner->image ? 'no-img' : ''; ?>">
                                <div class="content-column content-column_layout_a">
                                    <form class="form form_content_share">
                                        <div class="form-fields-group">
                                            <h4 class="form-fields-group__title">
                                                <?= \HCustomizations::earnInviteAndEarnTitle($partnerPage->viewData->partner); ?>
                                            </h4>
                                            <ul class="form-fields-list">
                                                <li class="form-fields-list__item">
                                                    <share-button
                                                        hover-text="<?= __('Copy link'); ?>"
                                                        success-text="<?= __('Copied!'); ?>"
                                                        clipboard-text="<?= $referralLink; ?>"
                                                        event-category="Using"
                                                        event-action="copyStoreUrlClick"
                                                        no-cut-link="true"
                                                    >
                                                    </share-button>
                                                </li>

                                                <li class="form-fields-list__item">
                                                    <?= __('or through the social network:'); ?>
                                                    <ul class="social-auth social-auth_content_share is-small">
                                                        <li class="social-auth__item">
                                                            <button class="social-button social-button_type_fb is-small js-action-share-fb"
                                                                    type="button"
                                                                    data-event-url="<?=$partnerPage->partner->clientEventProcessorUrl; ?>"
                                                                    data-share-url="<?= $referralLink; ?>">
                                                                <svg class="social-button__icon social-button__icon_type_fb">
                                                                    <use xlink:href="#fb"></use>
                                                                </svg>
                                                                <span class="social-button__text">
                                    <?= __('Share'); ?>
                                  </span>
                                                            </button>
                                                        </li>
                                                        <?php if (HLanguage::isRussian()): ?>
                                                        <li class="social-auth__item">
                                                            <button class="social-button social-button_type_vk is-small js-action-share-vk"
                                                                    data-event-url="<?=$partnerPage->partner->clientEventProcessorUrl; ?>"
                                                                    data-share-url="<?= $referralLink; ?>"
                                                                    type="button">
                                                                <svg class="social-button__icon social-button__icon_type_vk">
                                                                    <use xlink:href="#vk"></use>
                                                                </svg>
                                                                <span class="social-button__text">
                                      <?= __('Share'); ?>
                                    </span>
                                                            </button>
                                                        </li>
                                                        <?php endif; ?>
                                                        <li class="social-auth__item">
                                                            <button class="social-button social-button_type_email is-small js-show-modal"
                                                                    data-modal=".js-invite-modal" type="button">
                                                                <svg class="social-button__icon social-button__icon_type_email">
                                                                    <use xlink:href="#email"></use>
                                                                </svg>
                                                                <span class="social-button__text">
                                    <?= __('Email'); ?>
                                  </span>
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
                                    </form>
                                </div>

                                <?php if ($partnerPage->partner->image) {
                ?>
                                <div class="content-column content-column_layout_b">
                                    <div class="story story_content_share">
                                        <div class="story__thumbnail">
                                            <img class="story__image"
                                                 src="<?= $partnerPage->partner->image; ?>">
                                        </div>

                                        <div class="story__title">
                                            <?= $partnerPage->partner->title; ?>
                                        </div>
                                        <div class="story__meta">
                                            <a href="<?= $partnerPage->partner->url; ?>">
                                                <?= $partnerPage->partner->url; ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php
            } ?>
                            </div>
                        </div>
                        <?php
        } ?>
                        <?php if ($partnerPage->partner->isReferralPromoCodeEnabled && ($partnerPage->user->referralPromoCode ?? null)) {
            ?>
                        <div class="box box_content_share">
                            <div class="box__in content-columns content-columns_content_share <?= !$partnerPage->partner->image ? 'no-img' : ''; ?>">
                                <div class="content-column content-column_layout_a">
                                    <form class="form form_content_share">
                                        <div class="form-fields-group">
                                            <h4 class="form-fields-group__title">
                                                <?= __('You also can share your fixed personal promo-code for this store:'); ?>
                                            </h4>
                                            <ul class="form-fields-list">
                                                <li class="form-fields-list__item">
                                                    <share-button
                                                        hover-text="<?= __('Copy link'); ?>"
                                                        success-text="<?= __('Copied!'); ?>"
                                                        clipboard-text="<?= $partnerPage->user->referralPromoCode ?? null; ?>"
                                                        event-category="Using"
                                                        event-action="copyProductUrlClick"
                                                        no-cut-link="true"
                                                    >
                                                    </share-button>
                                                </li>

                                                <li class="form-fields-list__item" style="display: none;">
                                                    <?= __('or through the social network:'); ?>
                                                    <ul class="social-auth social-auth_content_share is-small">
                                                        <li class="social-auth__item">
                                                            <button class="social-button social-button_type_fb is-small js-action-share-fb"
                                                                    type="button"
                                                                    data-event-url="<?=$partnerPage->partner->clientEventProcessorUrl; ?>"
                                                                    data-share-url="<?= $partnerPage->user->referralPromoCode ?? null; ?>">
                                                                <svg class="social-button__icon social-button__icon_type_fb">
                                                                    <use xlink:href="#fb"></use>
                                                                </svg>
                                                                <span class="social-button__text">
                                      <?= __('Share'); ?>
                                    </span>
                                                            </button>
                                                        </li>
                                                        <?php if (HLanguage::isRussian()): ?>
                                                        <li class="social-auth__item">
                                                            <button class="social-button social-button_type_vk is-small js-action-share-vk"
                                                                    data-event-url="<?=$partnerPage->partner->clientEventProcessorUrl; ?>"
                                                                    data-share-url="<?= $partnerPage->user->referralPromoCode ?? null; ?>"
                                                                    type="button">
                                                                <svg class="social-button__icon social-button__icon_type_vk">
                                                                    <use xlink:href="#vk"></use>
                                                                </svg>
                                                                <span class="social-button__text">
                                        <?= __('Share'); ?>
                                      </span>
                                                            </button>
                                                        </li>
                                                        <?php endif; ?>
                                                        <li class="social-auth__item">
                                                            <button class="social-button social-button_type_email is-small js-show-modal"
                                                                    data-modal=".js-invite-modal" type="button">
                                                                <svg class="social-button__icon social-button__icon_type_email">
                                                                    <use xlink:href="#email"></use>
                                                                </svg>
                                                                <span class="social-button__text">
                                      <?= __('Email'); ?>
                                    </span>
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
                                    </form>
                                </div>

                                <?php if ($partnerPage->partner->image) {
                ?>
                                <div class="content-column content-column_layout_b">
                                    <div class="story story_content_share">
                                        <div class="story__thumbnail">
                                            <img class="story__image"
                                                 src="<?= $partnerPage->partner->image; ?>">
                                        </div>

                                        <div class="story__title">
                                            <?= $partnerPage->partner->title; ?>
                                        </div>
                                        <div class="story__meta">
                                            <a href="<?= $partnerPage->partner->url; ?>">
                                                <?= $partnerPage->partner->url; ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php
            } ?>
                            </div>
                        </div>
                        <?php
        } ?>

                    </div>
                    <?php
    } else {
        ?>
                    <div class="tab-content__header tab-content__header_content_image">
                        <img
                                class="tab-content__header-image"
                                src="/loyalty/images/invite-friend/icon.png"
                                srcset="/loyalty/images/invite-friend/icon@2x.png 2x"
                                alt="<?= __('Invite a friend'); ?>"
                                title="<?= __('Invite a friend'); ?>"
                        >
                        <h2 class="tab-content__title tab-content__title_viewtype_centered tab-content__title_viewtype_small i i_content_bold c-text">
                            <?=$partnerPage->partner->clientReferralSubtitle; ?>
                            <br><br>
                            <?=$partnerPage->partner->clientReferralHeading; ?>
                            <?=$partnerPage->partner->clientReferralMinAmountNotification; ?>
                        </h2>
                    </div>
                    <div class="tab-content__body">
                        <div class="form form_content_auth">
                            <fieldset class="form-fields-group">
                                <ul class="form-fields-list">
                                    <li class="form-fields-list__item">
                                        <ul class="social-auth social-auth_content_share">
                                            <li class="social-auth__item">
                                                <button class="social-button social-button_type_share social-button_viewtype_rectangle social-button_type_fb js-action-share-fb"
                                                        type="button"
                                                        data-event-url="<?=$partnerPage->partner->clientEventProcessorUrl; ?>"
                                                        data-share-url="<?= $referralLink; ?>">
                                                    <svg class="social-button__icon social-button__icon_type_fb">
                                                        <use xlink:href="#fb"></use>
                                                    </svg>
                                                    <span class="social-button__text">
                              <?= __('Share'); ?>
                            </span>
                                                </button>
                                            </li>
                                            <?php if (HLanguage::isRussian()): ?>
                                            <li class="social-auth__item">
                                                <button class="social-button social-button_type_share social-button_viewtype_rectangle social-button_type_vk js-action-share-vk"
                                                        data-event-url="<?=$partnerPage->partner->clientEventProcessorUrl; ?>"
                                                        data-share-url="<?= $referralLink; ?>" type="button">
                                                    <svg class="social-button__icon social-button__icon_type_vk">
                                                        <use xlink:href="#vk"></use>
                                                    </svg>
                                                    <span class="social-button__text">
                              <?= __('Share'); ?>
                            </span>
                                                </button>
                                            </li>
                                            <?php endif; ?>
                                            <li class="social-auth__item">
                                                <button class="social-button social-button_type_share social-button_viewtype_rectangle social-button_type_email js-show-modal"
                                                        data-modal=".js-invite-modal" type="button">
                                                    <svg class="social-button__icon social-button__icon_type_email">
                                                        <use xlink:href="#email"></use>
                                                    </svg>
                                                    <span class="social-button__text">
                              <?= __('Email'); ?>
                            </span>
                                                </button>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="form-fields-list__item">
                      <span class="delimiter c-text">
                        <?= __('or copy and send the link'); ?>:
                      </span>
                                    </li>
                                    <li class="form-fields-list__item">
                                        <dl class="field-composition">
                                            <dt class="field-composition__title">
                                                <label class="form-field-label" for="share-link">
                                                    <?= __('Copy the link'); ?>
                                                </label>
                                            </dt>
                                            <dd class="field-composition__content">
                                                <div class="form-field-box">
                                                    <input
                                                            type="text"
                                                            class="form-field form-field_type_text form-field_content_share-link c-primary-input"
                                                            name="share-link"
                                                            id="share-link"
                                                            value="<?= $referralLink; ?>"
                                                            readonly
                                                    >
                                                    <svg class="clipboard c-primary-fill js-clipboard js-tooltip "
                                                         data-tooltip-text="<?= __('Copy the link'); ?>"
                                                         data-clipboard-target="#share-link">
                                                        <use xlink:href="#clipboard"></use>
                                                    </svg>
                                                    <svg class="done c-primary-fill js-copy-done js-tooltip"
                                                         data-tooltip-text="<?= __('Copied'); ?>">
                                                        <use xlink:href="#done"></use>
                                                    </svg>
                                                </div>
                                            </dd>
                                        </dl>
                                    </li>
                                </ul>
                            </fieldset>
                        </div>
                    </div>
                    <?php
    } ?>
                </div>
            </div>
        </div>
    </div>
</invite>

@push('modals')
    <div class="modal modal_content_send-email js-invite-modal">
        <div class="modal__body">
            <ul class="horizontal-slider js-invite-modal-steps">
                <li class="horizontal-slider__item">
                    <form action="<?= $partnerPage->partner->inviteUrl; ?>" method="POST"
                          data-ajax-callback="INVITE_FORM_SUCCESS"
                          class="form form_content_send-email js-ajax-form">
                        <fieldset class="form-fields-group">
                            <legend class="form-fields-group__title"><?= __('Send an email invite'); ?></legend>
                            <input name="email" class="js-invite-emails" type="hidden">
                            <ul class="form-fields-list">
                                <li class="form-fields-list__item">
                                    <div class="field-composition">
                                        <div class="field-composition__content">
                                            <div class="form-field-box tags js-tagit">
                                                <label class="form-field-label form-field-label_viewtype_float c-primary-label"
                                                       for="recipient-email">
                                                    <?= __("Recipient's email"); ?>
                                                </label>
                                                <ul class="tags__list js-tagit-list">
                                                    <li class="tags__item tags__item_content_input">
                                                        <input size="1" class="tags__input js-tagit-hidden-input">
                                                    </li>
                                                </ul>
                                            </div>

                                        </div>
                                    </div>
                                </li>
                                <li class="form-fields-list__item">
                                    <div class="field-composition">
                                        <div class="field-composition__content">
                                            <div class="form-field-box">
                                                <input
                                                        type="text"
                                                        name="sender_name"
                                                        class="form-field form-field_type_text c-primary-input"
                                                        required
                                                        data-validate="true"
                                                        data-required="true"
                                                        value="<?=($partnerPage->user->name ?? null); ?>"
                                                >
                                                <label class="form-field-label form-field-label_viewtype_float c-primary-label"
                                                       for="sender-name">
                                                    <?= __('Senders name'); ?>
                                                </label>
                                            </div>

                                        </div>
                                    </div>
                                </li>
                                <li class="form-fields-list__item">
                                    <div class="field-composition">
                                        <div class="field-composition__content">
                                            <div class="form-field-box">
                      <textarea name="message" class="form-field form-field_type_textarea c-primary-input"
                                required><?=
                          __('Hello!  «%shop%» store is offering my friends a %discount%  discount on their first purchase. I was thinking you might be interested :)',
                              $partnerPage->partner->title,
                              $partnerPage->partner->referralRewardMessage);
                          ?></textarea>
                                                <label class="form-field-label form-field-label_viewtype_float form-field-label_type_textarea c-primary-label"
                                                       for="message">
                                                    <?= __('Message'); ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="form-fields-list__item form-fields-list__item_content_submit">
                                    <button class="button button_viewtype_modal c-primary-button"
                                            type="submit"><?= __('Send'); ?></button>
                                </li>
                            </ul>
                        </fieldset>
                    </form>
                </li>
                <li class="horizontal-slider__item">
                    <div class="form-success-send">
                        <div class="form-success-send__title">
                            <?= __('Link sent'); ?>
                        </div>
                        <div class="form-success-send__body i i_content_brand i_content_breakline c-primary-text">
                            <?= __('we sent your reference link and the message to %email%', ''); ?>
                            <span class="i__text js-invite-emails-sent-to">

              </span>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <svg class="modal__close js-close-modal">
            <use xlink:href="#popup-close"></use>
        </svg>
    </div>
@endpush


<?php endif; ?>
