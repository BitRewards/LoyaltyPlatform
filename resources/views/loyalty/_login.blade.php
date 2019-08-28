<?php
/**
 * @var App\Models\Partner
 * @var \App\DTO\PartnerPageData $partnerPage
 */
?>
<div class="tab-content tab-content_viewtype_centered is-hide js-tab-content" data-id="login">
  <div class="tab-content__in">
    <div class="tab-content__header">
      <?php if ($partnerPage->partner->isOborotPromoPartner) {
    ?>
        <h2 class="tab-content__title tab-content__title_viewtype_promo">
          <?= __('Добро пожаловать в демо-кабинет программы лояльности BitRewards.'); ?>
        </h2>
        <span class="tab-content__subtitle">
          <?= __('Авторизуйтесь через соцсеть или email и получите:'); ?>
        </span>
        <div class="tab-content__header-footer">
          <ul class="bullit-list">
            <li class="bullit-list__item">
              <svg class="bullit-list__icon c-primary-fill">
                <use xlink:href="#bullit"></use>
              </svg>
              <?= __('Скидку <b>5000</b> руб. на инструменты BitRewards'); ?>
            </li>
            <li class="bullit-list__item">
              <svg class="bullit-list__icon c-primary-fill">
                <use xlink:href="#bullit"></use>
              </svg>
              <?= __('Презентацию всех инструментов BitRewards'); ?>
            </li>
            <li class="bullit-list__item">
              <svg class="bullit-list__icon c-primary-fill">
                <use xlink:href="#bullit"></use>
              </svg>
              <?= __('Доступ к demo-кабинету программы лояльности'); ?>
            </li>
          </ul>
        </div>
      <?php
} elseif ($partnerPage->partner->isFightwearPromoPartner) {
        ?>
        <h2 class="tab-content__title tab-content__title_viewtype_promo">
            <?= __('Добро пожаловать в демо-кабинет программы лояльности BitRewards.'); ?>
        </h2>
        <div class="tab-content__header-footer">
          <ul class="bullit-list">
            <li class="bullit-list__item">
              <svg class="bullit-list__icon c-primary-fill">
                <use xlink:href="#bullit"></use>
              </svg>
              Авторизуйся и получи свой реферальный промокод
            </li>
            <li class="bullit-list__item">
              <svg class="bullit-list__icon c-primary-fill">
                <use xlink:href="#bullit"></use>
              </svg>
              Поделись промокодом и фотографией из магазина Fightwear со своими друзьями в социальных сетях.
            </li>
            <li class="bullit-list__item">
              <svg class="bullit-list__icon c-primary-fill">
                <use xlink:href="#bullit"></use>
              </svg>
              Получай по 100 рублей за пост и 5% от покупок друзей.
            </li>
          </ul>
        </div>
      <?php
    } elseif ($partnerPage->partner->isBazelevsPartner) {
      ?>
        <h2 class="tab-content__title i i_content_bold">
          <span class="i__text">Сюрпризы и&nbsp;бонусы от&nbsp;&laquo;Ёлки. Последние&raquo;</span>
        </h2>
        <span class="tab-content__subtitle tab-content__subtitle_content_elki">
          Зарегистрируйся в&nbsp;бонусной программе и&nbsp;получи билеты на&nbsp;фильм!
        </span>
          <?php
  } else {
              ?>
        <h2 class="tab-content__title i i_content_bold <?= $partnerPage->partner->isFiatReferrerEnabled ? 'tab-content__title_content_referral' : ''; ?>">
          <?php if ($partnerPage->partner->fiatWithdrawLoginTitle): ?>
             <?= $partnerPage->partner->fiatWithdrawLoginTitle; ?>
          <?php else: ?>
            <?php if ($partnerPage->partner->signUpBonusAmount) {
                  ?>
              <?php if ($partnerPage->partner->isFiatReferrerEnabled) {
                      ?>
                <?=  __("Get %fiat_referral_bonus% of the amount of referral's purchases on Visa and MasterCard cards", '<span class="i__text">'.$partnerPage->partner->fiatReferralBonus.'</span>'); ?>
              <?php
                  } elseif ($partnerPage->partner->signUpBonusAmount) {
                      ?>
                <?= __('You received %signup_bonus% for your visit!', '<span class="i__text">'.$partnerPage->partner->signUpBonus.'</span>'); ?>
              <?php
                  } else {
                      ?>
                <?= __('You received %signup_bonus% for your visit!', '<span class="i__text">'.$partnerPage->partner->signUpBonus.'</span>'); ?>
              <?php
                  } ?>
            <?php
              } else {
                  ?>
              <?= __('Welcome!'); ?>
            <?php
              } ?>
          <?php endif; ?>
          </h2>
        <?php if (!$partnerPage->partner->isAuthViaSocialNetworksHidden): ?>
          <span class="tab-content__subtitle">
            <?php if ($partnerPage->partner->isFiatReferrerEnabled) {
                  ?>
              <?= $partnerPage->partner->isSignupDisabled ? __('Sign in to receive referral links and earn.') : __('Sign up or sign in to receive referral links and earn.'); ?>
            <?php
              } else {
                  ?>
              <?= $partnerPage->partner->signUpBonusAmount ? __('Use one of the social networks to login:') : __('Login with social networks:'); ?>
            <?php
              } ?>
        </span>
        <?php endif; ?>
      <?php
          } ?>
    </div>
    <div class="tab-content__body">
      <?php if (!$partnerPage->partner->isBazelevsPartner && !$partnerPage->partner->isAuthViaSocialNetworksHidden) {
              ?>
        <ul class="form-fields-list">
          <li class="form-fields-list__item">
          <ul class="social-auth">
            <li class="social-auth__item">
              <button
                class="social-button social-button_type_fb js-social-auth-button"
                type="button"
                data-url="{{ $partnerPage->partner->oauthFBUrl }}"
                data-type="fb"
              >
                <svg class="social-button__icon social-button__icon_type_fb">
                  <use xlink:href="#fb"></use>
                </svg>
                <span class="social-button__text">
                  <?= __('Login with Facebook'); ?>
                </span>
              </button>
            </li>
            <?php if (HLanguage::isRussian()): ?>
            <li class="social-auth__item">
              <button
                class="social-button social-button_type_vk js-social-auth-button"
                type="button"
                data-url="{{ $partnerPage->partner->oauthVKUrl }}"
                data-type="vk"
              >
                <svg class="social-button__icon social-button__icon_type_vk">
                  <use xlink:href="#vk"></use>
                </svg>
                <span class="social-button__text">
                  <?= __('Login with VK'); ?>
                </span>
              </button>
            </li>
            <?php endif; ?>
            <li class="social-auth__item">
              <button
                class="social-button social-button_type_tw js-social-auth-button"
                type="button"
                data-url="{{ $partnerPage->partner->twitterAuthUrl }}"
                data-type="tw"
              >
                <svg class="social-button__icon social-button__icon_type_tw">
                  <use xlink:href="#tw"></use>
                </svg>
                <span class="social-button__text">
                  <?= __('Login with Twitter'); ?>
                </span>
              </button>
            </li>
            <li class="social-auth__item">
              <button
                class="social-button social-button_type_gp js-social-auth-button"
                type="button"
                data-url="{{ $partnerPage->partner->oauthGoogleUrl }}"
                data-type="gp"
              >
                <svg class="social-button__icon social-button__icon_type_gp">
                  <use xlink:href="#gp"></use>
                </svg>
                <span class="social-button__text">
                  <?= __('Login with Google+'); ?>
                </span>
              </button>
            </li>
          </ul>
          </li>
        </ul>
      <?php
          } ?>

      <?php if ($partnerPage->partner->isAuthMethodPhone()) {
              ?>
      @include('/loyalty/_login-by-phone')
    <?php
          } else {
              ?>
      @include('/loyalty/_login-by-email')
    <?php
          } ?>
    </div>
  </div>
</div>
