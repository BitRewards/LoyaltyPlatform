<?php
/**
 * @var \App\DTO\PartnerPageData
 */
?>

<div class="tab-content tab-content_viewtype_centered is-hide js-tab-content" data-id="confirmation-code-phone">
  <div class="tab-content__in">
    <div class="tab-content__header">
      <h2 class="tab-content__title">
        <?= __('Confirm phone number'); ?>
      </h2>
    </div>
    <div class="tab-content__body">
      <form class="form form_content_confirm js-ajax-form" action="{{ $partnerPage->partner->validateLoginCredentialUrl }}" data-ajax-callback="CONFIRMATION_CODE_SUCCESS" method="POST">
        <input type="hidden" name="phone" class="js-hide-email-or-phone">
        <fieldset class="form-fields-group">
          <ul class="form-fields-list">
            <li class="form-fields-list__item">
              <p class="form-remark">
                <?= __('We’ve sent the SMS with confirmation code. Enter it in the input form.'); ?>
              </p>
            </li>

            <li class="form-fields-list__item">
              <ul class="form-fields-groupbox form-fields-groupbox_content_auth-form">
                <li class="form-fields-groupbox__section">
                  <div class="field-composition">
                    <div class="field-composition__content">
                      <div class="form-field-box">
                        <input type="text" class="form-field form-field_type_text c-primary-input js-confirm-sms" name="token" data-validate="true" data-required="true" required>
                          <label class="form-field-label form-field-label_viewtype_float c-primary-label" for="confirm_sms">
                            <?= __('SMS code'); ?>
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
    </div>
  </div>
</div>
