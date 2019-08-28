<?php
/**
 * @var \App\DTO\PartnerPageData
 */
?>
<div class="tab-content tab-content_viewtype_centered tab-content_content_create-password is-hide js-tab-content" data-id="create-password" data-after-show-callback="NEWPASS_TRACK_EVENT">
  <div class="tab-content__in">
    <div class="tab-content__header">
      <h2 class="tab-content__title">
        <?= __('Create a password'); ?>
      </h2>
      <div class="tab-content__subtitle tab-content__subtitle_viewtype_small">
        <?= __('for account'); ?>
        <input type="button" class="form-field form-field_type_pure js-hide-email-or-phone" readonly>
      </div>
    </div>
    <div class="tab-content__body">
      <form action="{{ $partnerPage->partner->createPasswordUrl }}" class="form form_content_auth js-ajax-form" method="post" data-ajax-callback="PASSWORD_FORM_SUCCESS" data-ajax-before="PASSWORD_FORM_BEFORE_SEND" data-ajax-error-callback="PASSWORD_FORM_SEND_ERROR">
        <fieldset class="form-fields-group">
          <input type="hidden" name="{{ $partnerPage->partner->authMethod }}" class="js-hide-email-or-phone">
          <ul class="form-fields-list">
            <li class="form-fields-list__item">
              <p class="form-remark form-remark_viewtype_middle">
                <?= __('The password must be at least 7 characters long'); ?>
              </p>
            </li>

            <li class="form-fields-list__item">
              <ul class="form-fields-groupbox form-fields-groupbox_content_auth-form">
                <li class="form-fields-groupbox__section">
                  <div class="field-composition">
                    <div class="field-composition__content">
                      <div class="form-field-box">
                        <input 
                          type="password"
                          class="form-field form-field_type_text js-new-password c-primary-input"
                          name="password"
                          required
                          data-validate="true"
                          data-required="true"
                          >
                          <label class="form-field-label form-field-label_viewtype_float c-primary-label" for="password">
                            <?= __('Your password'); ?>
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
