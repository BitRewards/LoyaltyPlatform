<?php
/**
 * @var \App\DTO\PartnerPageData $partnerPage
 */
?>
<div class="tab-content tab-content_viewtype_centered is-hide js-tab-content" data-id="email-not-confirmed">
  <div class="tab-content__in">
    <div class="tab-content__header">
      <h2 class="tab-content__title">
        <?= __("Confirm your email!") ?>
      </h2>
      <span class="tab-content__subtitle tab-content__subtitle_viewtype_small i i_content_breakline">
        <span class="i__text"><p><?= __("It is necessary to confirm the email in order to use the points!") ?></p><p><?= __("Use the link in the email to do that") ?>.</p></span>
      </span>
    </div>

    <div class="tab-content__body">
      <form action="{{ $partnerPage->partner->checkEmailIsConfirmedUrl }}" class="form form_content_auth js-ajax-form" data-ajax-callback="CONFIRM_FORM_SUCCESS" method="post">
        <fieldset class="form-fields-group">
          <ul class="form-fields-list">
            <li class="form-fields-list__item">
              <ul class="form-fields-groupbox form-fields-groupbox_content_auth-form">
                <li class="form-fields-groupbox__section">
                  <button class="button button_type_block button_viewtype_primary c-primary-button js-loader" type="submit">
                  <span class="button__text">
                    <?= __("Check") ?>
                  </span>
                  </button>
                </li>
              </ul>
            </li>
          </ul>
        </fieldset>
      </form>

      <form action="{{ $partnerPage->viewData->emailConfirmationUrl }}" class="form form_content_send-confirm-email js-ajax-form" method="post">
        <fieldset class="form-fields-group">
          <input type="hidden" name="email" class="js-hide-email">
          <ul class="form-fields-list">
            <li class="form-fields-list__item form-fields-list__item_content_success-message">
              <div class="success-message">
                <?= __("The message is sent successfully - check your email") ?>
              </div>
            </li>
            <li class="form-fields-list__item form-fields-list__item_content_submit">
              <button class="button button_type_block button_viewtype_primary c-primary-button js-loader" type="submit">
                  <span class="button__text">
                    <?= __("Resend the email") ?>
                  </span>
              </button>
            </li>
          </ul>
        </fieldset>
      </form>
    </div>
  </div>
</div>
