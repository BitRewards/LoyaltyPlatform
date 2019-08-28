<?php
/**
 * @var \App\DTO\PartnerPageData
 */
?>
<form action="{{ $partnerPage->partner->checkCredentialStatusUrl }}" class="form form_content_auth js-ajax-form js-login-form" data-ajax-callback="LOGIN_FORM_SUCCESS" method="post">
<fieldset class="form-fields-group">
  <input type="hidden" name="int-phone">
  <ul class="form-fields-list">
    <li class="form-fields-list__item">
      <span class="delimiter">
        <?= __('or login using the phone number'); ?>
      </span>
    </li>
    <li class="form-fields-list__item">
      <ul class="form-fields-groupbox form-fields-groupbox_content_auth-form">
        <li class="form-fields-groupbox__section">
          <div class="field-composition">
            <div class="field-composition__content">
              <div class="form-field-box">
                <input
                  type="text"
                  class="form-field form-field_type_text js-email-or-phone c-primary-input js-int-phone"
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
    <?php if ($partnerPage->partner->isFiatReferrerEnabled) {
    ?>
          @include('/loyalty/_privacy', ['id' => 'privacy_policy'])
        <?php
} ?>
  </ul>
</fieldset>
</form>
