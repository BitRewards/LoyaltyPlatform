<?php
/**
 * @var \App\DTO\PartnerPageData
 */
?>

<div class="tab-content tab-content_viewtype_centered is-hide js-tab-content" data-id="confirmation-code-email">
  <div class="tab-content__in">
    <div class="tab-content__header">
      <h2 class="tab-content__title">
        <?= __('Confirm email'); ?>
      </h2>
    </div>
    <div class="tab-content__body">
      <form class="form form_content_confirm js-ajax-form js-confirmation-email" action="{{ $partnerPage->partner->validateLoginCredentialUrl }}" data-ajax-callback="CONFIRMATION_CODE_SUCCESS" method="POST">
        <input type="hidden" name="email" class="js-hide-email-or-phone">
        <fieldset class="form-fields-group">
          <ul class="form-fields-list">
            <li class="form-fields-list__item">
              <p class="form-remark">
                <?= __("We’ve sent the confirmation request to <strong class='js-confirm-email'></strong>. Check your mail and put the code in the input form."); ?>
              </p>
            </li>

            <li class="form-fields-list__item">
              <ul class="form-fields-groupbox form-fields-groupbox_content_auth-form">
                <li class="form-fields-groupbox__section">
                  <div class="field-composition">
                    <div class="field-composition__content">
                      <div class="form-field-box">
                        <input type="text" class="form-field form-field_type_text c-primary-input js-confirm-code" name="token" data-validate="true" data-required="true" required>
                          <label class="form-field-label form-field-label_viewtype_float c-primary-label" for="token">
                            <?= __('Сonfirmation сode'); ?>
                          </label>
                      </div>
                      <small class="input-error js-input-error"></small>
                    </div>
                  </div>
                </li>

                <li class="form-fields-groupbox__section">
                  <button class="button button_type_block button_viewtype_primary c-primary-button js-loader" type="submit">
                    <span class="button__text">
                      <?= __('Continue'); ?>
                    </span>
                  </button>
                </li>
              </ul>
            </li>
          </ul>
        </fieldset>
      </form>
      <form class="form form_content_confirm js-ajax-form js-resend-code" action="{{ $partnerPage->partner->checkCredentialStatusUrl }}" data-ajax-callback="RESEND_CODE_SUCCESS" method="POST">
        <input type="hidden" name="email" class="js-hide-email-or-phone">
        <fieldset class="form-fields-group">
          <ul class="form-fields-list">
            <li class="form-fields-list__item">
              <button class="link link_viewtype_resend-code c-primary-color js-loader" type="submit">
                <?= __('Send the code again'); ?>
              </button>
            </li>
          </ul>
        </fieldset>
      </form>
    </div>
  </div>
  <button class="back-button i i_content_mobile-no-display js-go-button" data-id="login">
    <svg class="back-button__icon">
      <use xlink:href="#back-arrow"></use>
    </svg>
    Назад
  </button>
</div>
