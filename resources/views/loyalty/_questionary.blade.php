<div class="tab-content tab-content_viewtype_left-margin is-hide js-tab-content" data-id="questionary">
  <div class="tab-content__in">
    <div class="scroller scroller_viewtype_top">
      <div class="scroller__in">
        <div class="tab-content__body">
          <form action="" class="form form_viewtype_horizontal form_content_questionary js-ajax-form" method="post">
            <fieldset class="form-fields-group">
              <legend class="form-fields-group__title">
                <?= __('Заполните анкету'); ?>
              </legend>

              <ul class="form-fields-list">
                <li class="form-fields-list__item">
                  <dl class="field-composition field-composition_viewtype_horizontal">
                    <dt class="field-composition__title">
                      <label class="form-field-label form-field-label_regular" for="fullname">
                        <?= __('Ф.И.О.'); ?>
                      </label>
                    </dt>
                    <dd class="field-composition__content">
                      <div class="form-field-box">
                        <input class="form-field form-field_type_text is-small" id="fullname">
                      </div>
                    </dd>
                  </dl>
                </li>

                <li class="form-fields-list__item">
                  <dl class="field-composition field-composition_viewtype_horizontal">
                    <dt class="field-composition__title">
                      <label class="form-field-label form-field-label_regular" for="company">
                        <?= __('Компания'); ?>
                      </label>
                    </dt>
                    <dd class="field-composition__content">
                      <div class="form-field-box">
                        <input class="form-field form-field_type_text is-small" id="company">
                      </div>
                    </dd>
                  </dl>
                </li>

                <li class="form-fields-list__item">
                  <dl class="field-composition field-composition_viewtype_horizontal">
                    <dt class="field-composition__title">
                      <label class="form-field-label form-field-label_regular" for="position">
                        <?= __('Должность'); ?>
                      </label>
                    </dt>
                    <dd class="field-composition__content">
                      <div class="form-field-box">
                        <input class="form-field form-field_type_text is-small" id="position">
                      </div>
                    </dd>
                  </dl>
                </li>

                <li class="form-fields-list__item">
                  <dl class="field-composition field-composition_viewtype_horizontal">
                    <dt class="field-composition__title">
                      <label class="form-field-label form-field-label_regular" for="phone">
                        <?= __('Мобильный телефон'); ?>
                      </label>
                    </dt>
                    <dd class="field-composition__content">
                      <div class="form-field-box">
                        <input class="form-field form-field_type_text is-small" id="phone" placeholder="+7">
                      </div>
                    </dd>
                  </dl>
                </li>

                <li class="form-fields-list__item">
                  <dl class="field-composition field-composition_viewtype_horizontal">
                    <dt class="field-composition__title">
                      <label class="form-field-label form-field-label_regular" for="website">
                        <?= __('Сайт'); ?>
                      </label>
                    </dt>
                    <dd class="field-composition__content">
                      <div class="form-field-box">
                        <input class="form-field form-field_type_text is-small" id="website">
                      </div>
                    </dd>
                  </dl>
                </li>

                <li class="form-fields-list__item form-fields-list__item_content_privacy">
                  <dl class="field-composition field-composition_viewtype_horizontal">
                    <dt class="field-composition__title">
                      &nbsp;
                    </dt>
                    <dd class="field-composition__content">
                      <div class="form-field-box">
                        <label for="accept_privacy" class="checkbox c-primary-checkbox">
                          <span class="checkbox__in">
                              <input type="checkbox" id="accept_privacy" name="accept_privacy" class="checkbox__input" checked>
                              <span class="checkbox__pseudo"></span>
                          </span>
                          <span class="checkbox__text">
                            <?= __('Я даю согласие на обработку <strong>персональных данных</strong>'); ?>
                          </span>
                        </label>
                      </div>
                    </dd>
                  </dl>
                </li>

                <li class="form-fields-list__item form-fields-list__item_content_submit">
                  <dl class="field-composition field-composition_viewtype_horizontal">
                    <dt class="field-composition__title">
                      &nbsp;
                    </dt>
                    <dd class="field-composition__content">
                      <button class="button button_viewtype_mw button_viewtype_primary c-primary-button js-loader" type="submit">
                        <span class="button__text">
                          <?= __('Завершить регистрацию'); ?>
                        </span>
                      </button>
                    </dd>
                  </dl>
                </li>

                <li class="form-fields-list__item form-fields-list__item_content_skip">
                  <dl class="field-composition field-composition_viewtype_horizontal">
                    <dl class="field-composition__title">
                      &nbsp;
                    </dl>
                    <dd class="field-composition__content">
                      <button class="button button_viewtype_link" type="button">
                        <span class="button__text">
                          <?= __('Пропустить'); ?>&nbsp;&gt;
                        </span>
                      </button>
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
  <button class="back-button i i_content_mobile-no-display js-go-button" data-id="login">
    <svg class="back-button__icon">
      <use xlink:href="#back-arrow"></use>
    </svg>
    <?= __('Back'); ?>
  </button>
</div>