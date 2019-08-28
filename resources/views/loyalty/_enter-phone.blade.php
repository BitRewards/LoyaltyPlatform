<?php
/**
 * @var App\Models\Partner
 * @var \App\DTO\PartnerPageData $partnerPage
 */
?>
<div class="tab-content tab-content_viewtype_centered is-hide js-tab-content" data-id="enter-phone">
  <div class="tab-content__in">
    <div class="tab-content__header">
      <h2 class="tab-content__title">
        <?= __('Enter your phone'); ?>
      </h2>
    </div>
    <div class="tab-content__body">
      <form action="{{ $partnerPage->partner->sendValidationTokenUrl }}" class="form form_content_auth js-ajax-form" data-ajax-callback="SUCCESS_ENTER_PARTNER_CREDENTIALS" method="post">
        <fieldset class="form-fields-group">
          <input type="hidden" name="int-phone">
          <ul class="form-fields-list">
            <li class="form-fields-list__item">
              <ul class="form-fields-groupbox form-fields-groupbox_content_auth-form">
                <li class="form-fields-groupbox__section">
                  <div class="field-composition">
                    <div class="field-composition__content">
                      <div class="form-field-box">
                        <input
                          type="text"
                          class="form-field form-field_type_text js-enter-phone c-primary-input js-int-phone"
                          name="phone"
                          data-validate="true"
                          data-required="true"
                        >
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
