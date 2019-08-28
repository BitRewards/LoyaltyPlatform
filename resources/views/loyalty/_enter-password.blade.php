<?php
/**
 * @var \App\DTO\PartnerPageData
 */
?>
<div class="tab-content tab-content_viewtype_centered is-hide js-tab-content" data-id="enter-password">
  <div class="tab-content__in">
    <div class="tab-content__header">
      <h2 class="tab-content__title">
        <?= __('Enter password'); ?>
      </h2>
    </div>
    <div class="tab-content__body">
      <form action="{{ $partnerPage->partner->loginUrl }}" class="form form_content_auth js-ajax-form" method="post" data-ajax-callback="PASSWORD_FORM_SUCCESS">
        <fieldset class="form-fields-group">
          <input type="hidden" name="{{ $partnerPage->partner->authMethod }}" class="js-hide-email-or-phone">
          <ul class="form-fields-list">
            <li class="form-fields-list__item">
              <ul class="form-fields-groupbox form-fields-groupbox_content_auth-form">
                <li class="form-fields-groupbox__section">
                  <div class="field-composition">
                    <div class="field-composition__content">
                      <div class="form-field-box">
                        <input 
                          type="password"
                          class="form-field form-field_type_text js-password c-primary-input"
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
            <li class="form-fields-list__item">
              <button class="link link_viewtype_forgott-password c-primary-color js-forgot-password-button" type="button">
                <?= __('I forgot my password'); ?>
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
    <?= __('Back'); ?>
  </button>
</div>
