<?php
/**
 * @var App\Models\Partner
 * @var \App\DTO\PartnerPageData $partnerPage
 */
?>

<div class="tab-content tab-content_withdraw is-hide js-tab-content" data-id="withdraw">
  <div class="tab-content__in tab-content__in_viewtype_mobile-wide">

    <div class="tab-content__body tab-content__body_viewtype_fixed">
      <div class="scroller">
        <div class="scroller__in">
          <h2 class="tab-content__title tab-content__title_viewtype_centered c-text">
            <?=__('Your balance:'); ?>
          </h2>
          <div class="tab-content__subtitle">
            @if ($partnerPage->user)
              <div class="balance c-primary-color"><span class="js-balance"><?= (int) $partnerPage->user->balanceAmount; ?></span> BIT</div>
              <div class="i i_content_balance"> = <?=$partnerPage->user->balanceInPartnerCurrency; ?></div>
            @endif
          </div>

          <form class="form js-withdraw-form" method="POST">
            <fieldset class="form-fields-group">
              <ul class="form-fields-list">
                <li class="form-fields-list__item">
                  <p class="form-remark">
                    <?=__('You may transfer your BIT tokens to your Ethereum wallet or to another shop.'); ?>
                  </p>
                </li>

                <li class="form-fields-list__item">
                  <ul class="form-fields-groupbox form-fields-groupbox_content_deposit">
                    <li class="form-fields-groupbox__section">
                      <div class="field-composition">
                        <div class="field-composition__content">
                          <div class="form-field-box">
                            <input type="text" class="form-field form-field_type_text js-ethereum c-primary-input" name="ethereum" data-validate="true" data-required="true" data-validate-mode="eth" required>
                              <label class="form-field-label form-field-label_viewtype_float c-primary-label" for="ethereum">
                                <?=__('Put Ethereum address here'); ?>
                              </label>
                          </div>
                          <small class="input-error js-input-error"></small>
                        </div>
                      </div>
                    </li>

                    <li class="form-fields-groupbox__section form-fields-groupbox__section_content_submit">
                      <button class="button button_type_block button_viewtype_primary c-primary-button js-loader" type="submit">
                        <span class="button__text">
                          <?=__('Set amount'); ?>
                        </span>
                      </button>
                    </li>
                  </ul>
                </li>
              </ul>
            </fieldset>
          </form>

          <div class="transaction">
            <h3 class="transaction__title">
              <?=__('Your withdraw transactions'); ?>
            </h3>

            <div class="transaction__body js-withdraw-transactions-list">
              @include('loyalty/_bitrewards-withdraw-transactions', ['transactions' => $partnerPage->bitrewardsPayoutTransactions])
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <button class="back-button js-go-button" type="button" aria-label="<?= __('Back'); ?>" data-id="balance">
    <svg class="back-button__icon">
      <use xlink:href="#back-arrow"></use>
    </svg>
  </button>
</div>
