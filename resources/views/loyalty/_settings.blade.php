<?php if (Auth::user()) {
    ?>
<div class="tab-content tab-content_settings is-hide js-tab-content" data-id="settings">
  <div class="tab-content__in tab-content__in_viewtype_mobile-wide">
    <div class="tab-content__header i i_content_mobile-hide">
      <button class="back-button back-button_viewtype_cabinet i i_content_mobile-no-display js-go-button" data-id="cabinet" type="button" aria-label="<?= __('Back'); ?>">
        <svg class="back-button__icon">
          <use xlink:href="#back-arrow"></use>
        </svg>
      </button>
      <h2 class="tab-content__title tab-content__title_viewtype_small">
        <?= __('Settings'); ?>
      </h2>
    </div>
    <div class="tab-content__body tab-content__body_viewtype_fixed">
      <div class="scroller">
        <div class="scroller__in">
          <form class="form form_content_settings" method="POST" action="">
            <fieldset class="form-fields-group">
              <ul class="form-fields-list">
                <li class="form-fields-list__item">
                  <dl class="field-composition field-composition_content_language">
                    <dt class="field-composition__title is-show">
                      <label class="form-field-label" for="language">
                        <?= __('Language'); ?>
                      </label>
                    </dt>
                    <dd class="field-composition__content">
                      <div class="form-field-box form-field-box_content_select">
                        <select class="form-field form-field_type_select">
                          <option><?= __('English'); ?></option>
                          <option><?= __('Russia'); ?></option>
                        </select>
                      </div>
                    </dd>
                  </dl>
                </li>

                <li class="form-fields-list__item">
                  <button type="button" class="link link_viewtype_standard i i_content_bold js-show-modal" data-modal=".js-change-password-modal">
                    <span class="i__text">
                      <?= __('Change password'); ?>
                    </span>
                  </button>
                </li>

                <li class="form-fields-list__item">
                  <dl class="field-composition">
                    <dt class="field-composition__title is-show">
                      <label class="form-field-label" for="language">
                        <?= __('Get notifications:'); ?>
                      </label>
                    </dt>
                    <dd class="field-composition__content">
                      <ul class="switch-group">
                        <li class="switch-group__item">
                          <label class="switch">
                            <input type="checkbox" class="switch__input" checked>
                            <span class="switch__pseudo"></span>
                            <span class="switch__text">
                              1@mail.ru
                            </span>
                          </label>
                        </li>

                        <li class="switch-group__item">
                          <label class="switch">
                            <input type="checkbox" class="switch__input" checked>
                            <span class="switch__pseudo"></span>
                            <span class="switch__text">
                              2@mail.ru
                            </span>
                          </label>
                        </li>
                      </ul>
                    </dd>
                  </dl>
                </li>
              </ul>
            </fieldset>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
} ?>
