<?php if (Auth::user()) {
    ?>
<div class="tab-content tab-content_profile is-hide js-tab-content" data-id="profile">
  <div class="tab-content__in tab-content__in_viewtype_mobile-wide">
    <div class="tab-content__header i i_content_mobile-hide">
      <button class="back-button back-button_viewtype_cabinet i i_content_mobile-no-display js-go-button" data-id="cabinet" type="button" aria-label="<?= __('Back'); ?>">
        <svg class="back-button__icon">
          <use xlink:href="#back-arrow"></use>
        </svg>
      </button>
      <h2 class="tab-content__title tab-content__title_viewtype_small">
        <?= __('Edit profile'); ?>
      </h2>
    </div>
    <div class="tab-content__body tab-content__body_viewtype_fixed">
      <div class="scroller">
        <div class="scroller__in">
          <div class="person person_viewtype_card" style="display:none;">
            <div class="person__avatar">
              <img src="/loyalty/images/content/avatar.png" width="120" height="120">
            </div>
            <div class="profile__desc">
              <ul class="profile-info">
                <li class="profile-info__item">
                  <dl class="def">
                    <dt class="def__key">
                      <?= __('First name'); ?>
                    </dt>
                    <dd class="def__value">
                      <?= \Auth::user()->getFirstName(); ?>
                    </dd>
                  </dl>
                </li>

                <li class="profile-info__item">
                  <dl class="def">
                    <dt class="def__key">
                      <?= __('Second name'); ?>
                    </dt>
                    <dd class="def__value">
                        <?= \Auth::user()->getSecondName(); ?>
                    </dd>
                  </dl>
                </li>

                @if(1 == 0)
                <li class="profile-info__item">
                  <dl class="def">
                    <dt class="def__key">
                      <?= __('Birth date'); ?>
                    </dt>
                    <dd class="def__value">
                        <?= \HDate::dateFull(null); ?>
                    </dd>
                  </dl>
                </li>
                @endif
              </ul>
            </div>
          </div>

          <form class="form form_content_bind-acc">
            <fieldset class="form-fields-group">
              <ul class="form-fields-list">
                <li class="form-fields-list__item">
                  <dl class="field-composition">
                    <dt class="field-composition__title is-show">
                      <label class="form-field-label">
                        <?= __('Linked accounts:'); ?>
                      </label>
                    </dt>
                    <dd class="field-composition__content">
                      <ul class="switch-group js-updatable" data-block-id="accounts-list">
                        @foreach(\Auth::user()->person->credentials as $credential)
                          <li class="switch-group__item">
                            <div class="bind-acc">
                              <div class="bind-acc__social">




                                @if($credential->type_id === \App\Models\Credential::TYPE_EMAIL)
                                  <div class="social-button social-button_type_mail is-small bind-acc__icon">
                                    <svg class="social-button__icon social-button__icon_type_mail">
                                      <use xlink:href="#mail"></use>
                                    </svg>
                                  </div>
                                  <span class="bind-acc__social-title">
                                    Email
                                  </span>
                                @endif

                                @if($credential->type_id === \App\Models\Credential::TYPE_PHONE)
                                    <div class="social-button social-button_type_phone is-small bind-acc__icon">
                                      <svg class="social-button__icon social-button__icon_type_phone">
                                        <use xlink:href="#phone"></use>
                                      </svg>
                                    </div>
                                    <span class="bind-acc__social-title">
                                      <?= __('Phone'); ?>
                                    </span>
                                @endif

                                @if($credential->type_id === \App\Models\Credential::TYPE_FACEBOOK)
                                  <div class="social-button social-button_type_fb is-small bind-acc__icon">
                                    <svg class="social-button__icon social-button__icon_type_fb">
                                      <use xlink:href="#fb"></use>
                                    </svg>
                                  </div>
                                  <span class="bind-acc__social-title">
                                    Facebook
                                  </span>
                                @endif

                                @if($credential->type_id === \App\Models\Credential::TYPE_GOOGLE)
                                  <div class="social-button social-button_type_gp is-small bind-acc__icon">
                                    <svg class="social-button__icon social-button__icon_type_gp">
                                      <use xlink:href="#gp"></use>
                                    </svg>
                                  </div>
                                  <span class="bind-acc__social-title">
                                    Google
                                  </span>
                                @endif

                                @if($credential->type_id === \App\Models\Credential::TYPE_VK)
                                  <div class="social-button social-button_type_vk is-small bind-acc__icon">
                                    <svg class="social-button__icon social-button__icon_type_vk">
                                      <use xlink:href="#vk"></use>
                                    </svg>
                                  </div>
                                  <span class="bind-acc__social-title">
                                    VKontakte
                                  </span>
                                @endif

                                @if($credential->type_id === \App\Models\Credential::TYPE_TWITTER)
                                  <div class="social-button social-button_type_tw is-small bind-acc__icon">
                                    <svg class="social-button__icon social-button__icon_type_tw">
                                      <use xlink:href="#tw"></use>
                                    </svg>
                                  </div>
                                  <span class="bind-acc__social-title">
                                    Twitter
                                  </span>
                                @endif


                              </div>

                              <div class="bind-acc__text">
                                <?= $credential->getTitle(); ?>
                              </div>

                              <div class="bind-acc__action">

                                <!--<button type="button" class="button button_viewtype_link c-pseudo-link">
                                      {{--<?= __('Unbind'); ?>--}}
                                    <svg class="button__icon">
                                      <use xlink:href="#popup-close"></use>
                                    </svg>
                                  </button>
                                </div>-->

                            </div>
                          </li>
                        @endforeach
                      </ul>
                    </dd>
                  </dl>
                </li>

                <li class="form-fields-list__item popover-context popover-context_content_add">
                  <button type="button" class="button button_viewtype_primary button_content_add c-primary-button js-show-popover" data-popover=".js-bind-popover">
                    <svg class="button__icon" aria-hidden="true">
                      <use xlink:href="#plus"></use>
                    </svg>
                    <span class="button__text">
                      <?= __('Add'); ?>
                    </span>
                  </button>

                  <div class="popover js-bind-popover <?= \Auth::user()->person->credentials->count() >= 3 ? 'top' : ''; ?>">
                    <ul class="popover__list">
                      
                      <li class="popover__item">
                        <button class="bind-acc bind-acc_viewtype_popover js-show-modal" type="button" data-modal=".js-confirm-email-modal">
                          <div class="bind-acc__social">
                            <svg class="bind-acc__plus">
                              <use xlink:href="#plus"></use>
                            </svg>
                            <div class="social-button social-button_type_mail is-small bind-acc__icon">
                              <svg class="social-button__icon social-button__icon_type_mail">
                                <use xlink:href="#mail"></use>
                              </svg>
                            </div>
                            <span class="bind-acc__social-title">
                              Email
                            </span>
                          </div>
                        </button>
                      </li>

                      <li class="popover__item">
                        <button class="bind-acc bind-acc_viewtype_popover js-show-modal" type="button" data-modal=".js-confirm-phone-modal">
                          <div class="bind-acc__social">
                            <svg class="bind-acc__plus">
                              <use xlink:href="#plus"></use>
                            </svg>
                            <div class="social-button social-button_type_phone is-small bind-acc__icon">
                              <svg class="social-button__icon social-button__icon_type_phone">
                                <use xlink:href="#phone"></use>
                              </svg>
                            </div>
                            <span class="bind-acc__social-title">
                              <?= __('Phone'); ?>
                            </span>
                          </div>
                        </button>
                      </li>

                      <li class="popover__item">
                        <button
                                class="bind-acc bind-acc_viewtype_popover js-social-auth-button"
                                type="button"
                                data-url="{{ $partnerPage->partner->oauthFBUrl }}"
                        >
                          <div class="bind-acc__social">
                            <svg class="bind-acc__plus">
                              <use xlink:href="#plus"></use>
                            </svg>
                            <div class="social-button social-button_type_fb is-small bind-acc__icon">
                              <svg class="social-button__icon social-button__icon_type_fb">
                                <use xlink:href="#fb"></use>
                              </svg>
                            </div>
                            <span class="bind-acc__social-title">
                              Facebook
                            </span>
                          </div>
                        </button>
                      </li>

                      <li class="popover__item">
                        <button
                                class="bind-acc bind-acc_viewtype_popover js-social-auth-button"
                                type="button"
                                data-url="{{ $partnerPage->partner->oauthVKUrl }}"
                        >
                          <div class="bind-acc__social">
                            <svg class="bind-acc__plus">
                              <use xlink:href="#plus"></use>
                            </svg>
                            <div class="social-button social-button_type_vk is-small bind-acc__icon">
                              <svg class="social-button__icon social-button__icon_type_vk">
                                <use xlink:href="#vk"></use>
                              </svg>
                            </div>
                            <span class="bind-acc__social-title">
                              VKontakte
                            </span>
                          </div>
                        </button>
                      </li>

                      <li class="popover__item">
                        <button
                                class="bind-acc bind-acc_viewtype_popover js-social-auth-button"
                                type="button"
                                data-url="{{ $partnerPage->partner->twitterAuthUrl }}"
                        >
                          <div class="bind-acc__social">
                            <svg class="bind-acc__plus">
                              <use xlink:href="#plus"></use>
                            </svg>
                            <div class="social-button social-button_type_tw is-small bind-acc__icon">
                              <svg class="social-button__icon social-button__icon_type_tw">
                                <use xlink:href="#tw"></use>
                              </svg>
                            </div>
                            <span class="bind-acc__social-title">
                              Twitter
                            </span>
                          </div>
                        </button>
                      </li>

                      <li class="popover__item">
                        <button
                                class="bind-acc bind-acc_viewtype_popover js-social-auth-button"
                                type="button"
                                data-url="{{ $partnerPage->partner->oauthGoogleUrl }}"
                        >
                          <div class="bind-acc__social">
                            <svg class="bind-acc__plus">
                              <use xlink:href="#plus"></use>
                            </svg>
                            <div class="social-button social-button_type_gp is-small bind-acc__icon">
                              <svg class="social-button__icon social-button__icon_type_gp">
                                <use xlink:href="#gp"></use>
                              </svg>
                            </div>
                            <span class="bind-acc__social-title">
                              Google
                            </span>
                          </div>
                        </button>
                      </li>
                    </ul>
                  </div>
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
