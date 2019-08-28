<?php
/**
 * @var \App\DTO\PartnerPageData
 */
?>
<form action="{{ $partnerPage->partner->checkCredentialStatusUrl }}" class="form form_content_auth js-ajax-form js-login-form" data-ajax-callback="LOGIN_FORM_SUCCESS" data-ajax-before="LOGIN_FORM_BEFORE_SEND" data-ajax-error-callback="LOGIN_FORM_SEND_ERROR" method="post">
<fieldset class="form-fields-group">
  <ul class="form-fields-list">
    <li class="form-fields-list__item">
      <span class="delimiter">
        <?php if ($partnerPage->partner->isBazelevsPartner) {
    ?>
          Введи свой email для регистрации
        <?php
} else {
        ?>
          <?php if (!$partnerPage->partner->isAuthViaSocialNetworksHidden): ?>
            <?= __('or login using your email'); ?>
          <?php endif; ?>
        <?php
    } ?>
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
                  class="form-field form-field_type_text js-email-or-phone c-primary-input"
                  name="email"
                  data-validate="true"
                  data-required="true"
                  data-validate-mode="email"
                  required
                  >
                  <label class="form-field-label form-field-label_viewtype_float c-primary-label" for="email">
                    <?php if ($partnerPage->partner->isBazelevsPartner) {
        ?>
                      Твой email
                    <?php
    } else {
        ?>
                      <?= __('Your email'); ?>
                    <?php
    } ?>
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

    <?php if ($partnerPage->partner->isBazelevsPartner) {
        ?>
      <li class="form-fields-list__item">
        <label for="privacy_policy" class="checkbox">
          <span class="checkbox__in">
              <input type="checkbox" id="privacy_policy" name="privacy_policy" class="checkbox__input">
              <span class="checkbox__pseudo"></span>
          </span>
          <span class="checkbox__text">Я согласен с
            <a class="link link_viewtype_standard" href="http://elki-film.ru/policy.html" target="_blank">политикой конфиденциальности</a>
          </span>
        </label>
      </li>
    <?php
    } elseif ($partnerPage->partner->isFiatReferrerEnabled) {
        ?>
      @include('/loyalty/_privacy', ['id' => 'privacy_policy'])
    <?php
    } ?>
  </ul>
</fieldset>
</form>
