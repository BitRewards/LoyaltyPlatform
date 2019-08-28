<?php
/**
 * @var App\Models\Partner
 * @var \App\DTO\PartnerPageData $partnerPage
 */
?>

<div class="modal-overlay js-modal-overlay">
  <div class="modal modal_content_auth js-auth-modal">
    <h4 class="modal__title">
    </h4>
    <div class="modal__body">
      <p class="modal__text">
        <?= __('You can use your bonuses after logging in'); ?>.
      </p>
    </div>
    <div class="modal__footer">
      <button class="button button_viewtype_modal c-primary-button js-go-button" data-id="login"><?= __('Login'); ?></button>
    </div>
  </div>

  <div class="modal modal_content_auth js-signup-disabled-modal">
    <h4 class="modal__title">
    </h4>
    <div class="modal__body">
      <p class="modal__text">
        <?= __("The user with e-mail <strong class='js-confirm-email'></strong> is unregistered."); ?>
      </p>
    </div>
    <div class="modal__footer">
      <button class="button button_viewtype_modal c-primary-button js-go-button" data-id="login"><?= __('Login'); ?></button>
    </div>
  </div>

  @stack('modals')

  <div class="modal modal_content_ask-questions js-ask-question-modal">
    <div class="modal__body">
      <ul class="horizontal-slider js-ask-question-modal-steps">
        <li class="horizontal-slider__item">
          <form action="<?=$partnerPage->partner->clientSupportUrl; ?>" method="post" class="form form_content_ask-question js-ajax-form" data-ajax-callback="SEND_QUESTION_SUCCESS">
            <fieldset class="form-fields-group">
              <legend class="form-fields-group__title"><?= __('Ask a question'); ?></legend>
              <ul class="form-fields-list">
                <li class="form-fields-list__item">
                  <div class="field-composition">
                    <div class="field-composition__content">
                      <div class="form-field-box">
                        <input
                          type="text"
                          name="email"
                          class="form-field form-field_type_text js-ask-email-input c-primary-input"
                          required
                          data-validate="true"
                          data-required="true"
                          data-validate-mode="email"
                          value="<?= $partnerPage->user->email ?? null; ?>"
                        >
                        <label class="form-field-label form-field-label_viewtype_float c-primary-label" for="email">
                          <?= __('Response email'); ?>
                        </label>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="form-fields-list__item">
                  <div class="field-composition">
                    <div class="field-composition__content">
                      <div class="form-field-box">
                        <textarea name="message" class="form-field form-field_type_textarea c-primary-input" required data-validate="true" data-required="true"></textarea>
                        <label class="form-field-label form-field-label_viewtype_float form-field-label_type_textarea c-primary-label" for="question">
                          <?= __('Ask a question'); ?>
                        </label>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="form-fields-list__item form-fields-list__item_content_submit">
                  <button class="button button_viewtype_modal c-primary-button" type="submit">
                    <?= __('Send'); ?>
                  </button>
                </li>
              </ul>
            </fieldset>
          </form>
        </li>
        <li class="horizontal-slider__item">
          <div class="form-success-send">
            <div class="form-success-send__title">
              <?= __('Thank you'); ?>
            </div>
            <div class="form-success-send__body i i_content_brand i_content_breakline c-primary-text">
              <?= __('We will respond through email'); ?>
              <span class="i__text js-send-email c-primary-color">

              </span>
            </div>
          </div>
        </li>
      </ul>
    </div>
    <svg class="modal__close js-close-modal">
      <use xlink:href="#popup-close"></use>
    </svg>
  </div>

  <div class="modal modal_content_text js-text-modal">
    <div class="modal__body">
      <svg class="modal__success-icon c-primary-fill js-text-modal-success-icon" style="display: none">
        <use xlink:href="#ok2"></use>
      </svg>

      <h4 class="modal__title js-text-modal-title">

      </h4>
      <p class="modal__text js-text-modal-text">

      </p>
    </div>
    <div class="modal__footer">
      <button class="button button_viewtype_modal c-primary-button js-close-modal">OK</button>
    </div>

    <svg class="modal__close js-close-modal">
      <use xlink:href="#popup-close"></use>
    </svg>
  </div>



  <div class="modal modal_content_add-card js-add-card-modal">
    <h4 class="modal__title js-add-card-modal-title">
        <?= __('Activate card'); ?>
    </h4>
    <div class="modal__body">
      <ul class="horizontal-slider js-add-card-modal-steps">
        <li class="horizontal-slider__item">
          <form action="<?=$partnerPage->partner->clientEventAcquireCodeUrl; ?>" method="post" class="form form_content_ask-question js-ajax-form" data-ajax-callback="ADD_CARD_SUCCESS">
            <fieldset class="form-fields-group">
              <legend class="form-fields-group__title">
                  <?=$partnerPage->partner->discountInsteadOfLoyalty
                      ? __('Activate discount card')
                      : __('Activate loyalty card');
                  ?>
              </legend>
              <ul class="form-fields-list">
                <li class="form-fields-list__item">
                  <div class="field-composition">
                    <div class="field-composition__content">
                      <div class="form-field-box">
                        <input
                                type="text"
                                name="token"
                                class="form-field form-field_type_text c-primary-input"
                                required
                                data-validate="true"
                                data-required="true"
                                value=""
                        >
                        <label class="form-field-label form-field-label_viewtype_float c-primary-label" for="card">
                            <?= __('Card Number'); ?>
                        </label>
                      </div>
                      <small class="input-error js-input-error"></small>
                    </div>
                  </div>
                </li>
                <li class="form-fields-list__item form-fields-list__item_content_submit">
                  <button class="button button_viewtype_modal c-primary-button" type="submit">
                      <?= __('Activate card'); ?>
                  </button>
                </li>
              </ul>
            </fieldset>
          </form>
        </li>
        <li class="horizontal-slider__item">
          <div class="form-success-send">
            <div class="form-success-send__title">
                <?= __('Congratulations!'); ?>
            </div>
            <div class="form-success-send__body js-add-card-modal-success-text c-primary-text">
                <?= __('You have activated your card'); ?>
            </div>
          </div>
        </li>
      </ul>
    </div>
    <svg class="modal__close js-close-modal">
      <use xlink:href="#popup-close"></use>
    </svg>
  </div>


  <div class="modal modal_content_promocode js-promocode-modal">
    <h4 class="modal__title js-promocode-modal-title">
      <?= __('Enter the promo code'); ?>
    </h4>
    <div class="modal__body">
      <ul class="horizontal-slider js-promocode-modal-steps">
        <li class="horizontal-slider__item">
          <form action="<?=$partnerPage->partner->clientEventAcquireCodeUrl; ?>" method="post" class="form form_content_ask-question js-ajax-form" data-ajax-callback="PROMOCODE_SUCCESS">
            <fieldset class="form-fields-group">
              <legend class="form-fields-group__title"><?= __('Enter the promo code'); ?></legend>
              <ul class="form-fields-list">
                <li class="form-fields-list__item">
                  <div class="field-composition">
                    <div class="field-composition__content">
                      <div class="form-field-box">
                        <input
                          type="text"
                          name="token"
                          class="form-field form-field_type_text c-primary-input"
                          required
                          data-validate="true"
                          data-required="true"
                          value=""
                        >
                        <label class="form-field-label form-field-label_viewtype_float c-primary-label" for="token">
                          <?= __('Promo code'); ?>
                        </label>
                      </div>
                      <small class="input-error js-input-error"></small>
                    </div>
                  </div>
                </li>
                <li class="form-fields-list__item form-fields-list__item_content_submit">
                  <button class="button button_viewtype_modal c-primary-button" type="submit">
                    <?= __('Redeem'); ?>
                  </button>
                </li>
              </ul>
            </fieldset>
          </form>
        </li>
        <li class="horizontal-slider__item">
          <div class="form-success-send">
            <div class="form-success-send__title">
              <?= __('Success!'); ?>
            </div>
            <div class="form-success-send__body js-promocode-modal-success-text c-primary-text">
              <?= __('You received a bonus'); ?>
            </div>
          </div>
        </li>
      </ul>
    </div>
    <svg class="modal__close js-close-modal">
      <use xlink:href="#popup-close"></use>
    </svg>
  </div>

  <?php if (isset($partnerPage->partner->brwAmount)) {
                      ?>
  <div class="modal modal_content_ethereum-output js-ethereum-output-modal">
    <h4 class="modal__title">
      <?= __('Enter your Ethereum wallet address'); ?>
    </h4>
    <div class="modal__body">
      <form class="form form_content_ethereum-output js-ajax-form" action="<?=$partnerPage->partner->clientRewardBitRewardsPayoutUrl; ?>" method="POST" data-ajax-callback="ETHEREUM_OUTPUT_SUCCESS">
        <fieldset>
          <ul class="form-fields-list">
            <li class="form-fields-list__item">
              <div class="field-composition">
                <div class="field-composition__content">
                  <div class="form-field-box">
                    <input type="text" class="form-field form-field_type_text c-primary-input" placeholder="<?= __('Your Ethereum address'); ?>" name="eth_address" data-required="true" data-validate="true">
                  </div>
                  <small class="input-error js-input-error"></small>
                </div>
              </div>
            </li>
            <li class="form-fields-list__item">
              <div id="recaptcha" class="g-recaptcha"></div>
            </li>
            <li class="form-fields-list__item form-fields-list__item_content_submit">
              <button type="submit" class="button button_type_block button_viewtype_primary is-small js-btw-out c-primary-button" disabled>
                <span class="button__text  js-updatable" data-block-id="brw-payout-modal-button">
                  <?= __('Withdraw %s BIT', $partnerPage->partner->brwAmount); ?>
                </span>
              </button>
            </li>
          </ul>
        </fieldset>
      </form>
    </div>
    <svg class="modal__close js-close-modal">
      <use xlink:href="#popup-close"></use>
    </svg>
  </div>
  <?php
                  } ?>


  <?php if ($partnerPage->partner->isBitRewardEnabled && $partnerPage->user) :?>
  <div class="modal modal_content_withdraw js-withdraw-modal" data-close-callback="WITHDRAW_RESET">
    <h4 class="modal__title">
      <ul class="steps js-withdraw-modal-steps">
        <li class="steps__item">
          <a class="steps__link c-step" data-step="1">
            1
          </a>
        </li>
        <li class="steps__item">
          <a class="steps__link c-step" data-step="2">
            2
          </a>
        </li>
      </ul>
    </h4>
    <div class="modal__body">
      <ul class="horizontal-slider js-withdraw-modal-slider">
        <li class="horizontal-slider__item">
          <form action="<?=$partnerPage->partner->clientRewardBitRewardsPayoutUrl; ?>" method="post" class="form form_content_withdraw js-ajax-form" data-ajax-callback="WITHDRAW_SUCCESS">
            <fieldset class="form-fields-group">
              <legend class="form-fields-group__title">
                <?= __('Fill in the amount input form with number of BIT you want to get on your Ethereum wallet'); ?>
              </legend>
              <input type="hidden" name="withdraw_eth" class="js-withdraw-eth-input">
              <input type="hidden" class="js-withdraw-fee-type" value="<?=$partnerPage->partner->withdrawFeeType; ?>">
              <input type="hidden" class="js-withdraw-fee-value" value="<?=$partnerPage->partner->withdrawFeeValue; ?>">
              <input type="hidden" class="js-withdraw-amount-min" value="<?=$partnerPage->partner->withdrawAmountMin; ?>">
              <input type="hidden" class="js-withdraw-amount-max" value="<?=$partnerPage->partner->withdrawAmountMax; ?>">
              <ul class="form-fields-list">
                <li class="form-fields-list__item">
                  <div class="field-composition">
                    <div class="field-composition__content">
                      <div class="form-field-box i i_content_bit">
                        <input
                          type="text"
                          name="token_amount"
                          class="form-field form-field_type_text form-field_content_withdraw c-primary-input js-withdraw-amount"
                          required
                          data-validate="true"
                          data-required="true"
                          value="<?= $partnerPage->partner->withdrawAmountMin; ?>"
                        >
                        <span class="i__text">BIT</span>
                      </div>
                      <small class="input-error js-input-error"></small>
                    </div>
                  </div>
                </li>
                <li class="form-fields-list__item">
                  <div class="withdraw-terms">
                    <h6 class="withdraw-terms__title">
                      <?= __('Store terms:'); ?>
                    </h6>
                    <ul class="withdraw-terms__list">
                      <li class="withdraw-terms__item">
                        <button type="button" class="button button_viewtype_badge js-withdraw-button" data-amount="<?= $partnerPage->partner->withdrawAmountMin; ?>">
                          <span class="button__text">
                            <?= __('Minimum <b>%d BIT</b>', $partnerPage->partner->withdrawAmountMin); ?>
                          </span>
                        </button>
                      </li>
                      <li class="withdraw-terms__item">
                        <button type="button" class="button button_viewtype_badge js-withdraw-button" data-amount="<?= $partnerPage->partner->withdrawAmountMax; ?>">
                          <span class="button__text">
                            <?= __('Maximum <b>%d BIT</b>', $partnerPage->partner->withdrawAmountMax); ?>
                          </span>
                        </button>
                      </li>
                    </ul>
                  </div>
                </li>
                <li class="form-fields-list__item">
                  <div class="calculator">
                    <ul class="calculator__list">
                      <li class="calculator__item">
                        <dl class="calculator__position">
                          <dt class="calculator__key i i_content_fee">
                            <span class="i__text"><?= $partnerPage->partner->withdrawFeeValue; ?> <?= 'percent' === $partnerPage->partner->withdrawFeeType ? '%' : 'BIT'; ?></span> <?= __('shop withdrawal fee'); ?>
                          </dt>
                          <dd class="calculator__value">
                            <span class="js-withdraw-fee"></span> BIT
                          </dd>
                        </dl>
                      </li>

                      <li class="calculator__item">
                        <dl class="calculator__position">
                          <dt class="calculator__key">
                            <?= __('Amount charged'); ?>
                          </dt>
                          <dd class="calculator__value">
                            <span class="js-withdraw-amount-charged"></span> BIT
                          </dd>
                        </dl>
                      </li>
                    </ul>

                    <dl class="calculator__total">
                      <dt class="calculator__total-text">
                        <?= __('Total amount to be credited'); ?>
                      </dt>
                      <dd class="calculator__total-sum">
                        <span class="js-withdraw-total"></span> BIT
                      </dd>
                    </dl>
                  </div>
                </li>
                <li class="form-fields-list__item form-fields-list__item_content_submit">
                  <button class="button button_viewtype_modal c-primary-button js-loader" type="submit">
                    <?= __("Transfer <span class='js-withdraw-total'></span> BIT"); ?>
                  </button>
                </li>
              </ul>
            </fieldset>
          </form>
        </li>

        <li class="horizontal-slider__item">
          <div class="incut c-incut">
            <div class="incut__in">
              <svg class="incut__icon c-primary-fill">
                <use xlink:href="#ok2"></use>
              </svg>
              <p class="incut__text i i_content_eth-address">
                <strong>
                    <?= __('You have sent'); ?> <span class="js-withdraw-total"></span> BIT
                      <span class="js-withdraw-magic-details"><?= __("to other BitRewards shop's Ethereum-address:"); ?></span>
                      <span class="js-withdraw-no-magic-details"><?= __('to other Ethereum-address:'); ?></span>
                </strong>
                <br>
                <span class="i__text js-withdraw-eth-address"></span>
              </p>
            </div>
          </div>

          <div class="form form_content_withdraw">
            <fieldset class="form-fields-group" >
              <legend class="form-fields-group__title js-withdraw-magic-details">
                <?= __('Copy this magic number to shop where you send BIT'); ?>
              </legend>
              <ul class="form-fields-list">
                <li class="form-fields-list__item js-withdraw-magic-details">
                  <dl class="field-composition">
                    <dt class="field-composition__title">
                      <label class="form-field-label" for="magic">
                        <?= __('Copy magic number'); ?>
                      </label>
                    </dt>
                    <dd class="field-composition__content">
                      <div class="form-field-box">
                        <input
                          type="text"
                          class="form-field form-field_type_text form-field_content_clipboard c-primary-input js-withdraw-magic"
                          name="magic"
                          id="magic-withdraw"
                          value=""
                          readonly
                        >
                        <svg class="clipboard c-primary-fill js-clipboard js-tooltip" data-tooltip-text="<?= __('Copy magic number'); ?>" data-clipboard-target="#magic-withdraw">
                          <use xlink:href="#clipboard"></use>
                        </svg>
                        <svg class="done c-primary-fill js-copy-done js-tooltip" data-tooltip-text="<?= __('Copied!'); ?>">
                          <use xlink:href="#done"></use>
                        </svg>
                      </div>
                    </dd>
                  </dl>
                </li>

                <li class="form-fields-list__item js-withdraw-magic-details">
                  <div class="content-columns content-columns_content_instruction">
                    <div class="content-column content-column_layout_a">
                      <ol class="instruction">
                        <li class="instruction__item">
                          <?= __('Copy this magic number;'); ?>
                        </li>
                        <li class="instruction__item">
                          <?= __('Go to the shop, where you send this BITs;'); ?>
                        </li>
                        <li class="instruction__item">
                          <?= __('Go to the <strong>Deposit BIT > From other BitRewards shop;</strong>'); ?>
                        </li>
                        <li class="instruction__item">
                          <?= __('Paste this magic number to the input-form.'); ?>
                        </li>
                      </ol>
                    </div>

                    <div class="content-column content-column_layout_b">
                      <img class="" src="/loyalty/images/content/withdraw/image.png" srcset="/loyalty/images/content/withdraw/image@2x.png 2x">
                    </div>
                  </div>
                </li>

                <li class="form-fields-list__item">
                  <button type="button" class="button button_viewtype_modal c-primary-button js-close-modal">
                    <?= __('Done'); ?>
                  </button>
                </li>
              </ul>
            </fieldset>
          </div>
        </li>
      </ul>
    </div>
    <svg class="modal__close js-close-modal">
      <use xlink:href="#popup-close"></use>
    </svg>
  </div>

  <div class="modal modal_content_withdraw js-sent-withdraw-modal" data-close-callback="WITHDRAW_RESET">
    <h4 class="modal__title">

    </h4>
    <div class="modal__body">
      <ul class="horizontal-slider">
        <li class="horizontal-slider__item">
          <div class="incut c-incut">
            <div class="incut__in">
              <svg class="incut__icon c-primary-fill">
                <use xlink:href="#ok2"></use>
              </svg>
              <p class="incut__text i i_content_eth-address">
                <strong>
                    <?= __('You have sent'); ?> <span class="js-sent-withdraw-total"></span> BIT <?= __("to other BitRewards shop's Ethereum-address:"); ?>
                </strong>
                <br>
                <span class="i__text js-sent-withdraw-eth-address"></span>
              </p>
            </div>
          </div>

          <div class="form form_content_withdraw">
            <fieldset class="form-fields-group" >
              <legend class="form-fields-group__title">
                  <?= __('Copy this magic number to shop where you send BIT'); ?>
              </legend>
              <ul class="form-fields-list">
                <li class="form-fields-list__item">
                  <dl class="field-composition">
                    <dt class="field-composition__title">
                      <label class="form-field-label" for="magic">
                          <?= __('Copy magic number'); ?>
                      </label>
                    </dt>
                    <dd class="field-composition__content">
                      <div class="form-field-box">
                        <input
                                type="text"
                                class="form-field form-field_type_text form-field_content_clipboard c-primary-input js-sent-withdraw-magic"
                                name="magic"
                                id="magic"
                                value=""
                                readonly
                        >
                        <svg class="clipboard c-primary-fill js-clipboard js-tooltip" data-tooltip-text="<?= __('Copy magic number'); ?>" data-clipboard-target="#magic">
                          <use xlink:href="#clipboard"></use>
                        </svg>
                        <svg class="done c-primary-fill js-copy-done js-tooltip" data-tooltip-text="<?= __('Copied!'); ?>">
                          <use xlink:href="#done"></use>
                        </svg>
                      </div>
                    </dd>
                  </dl>
                </li>

                <li class="form-fields-list__item">
                  <div class="content-columns content-columns_content_instruction">
                    <div class="content-column content-column_layout_a">
                      <ol class="instruction">
                        <li class="instruction__item">
                            <?= __('Copy this magic number;'); ?>
                        </li>
                        <li class="instruction__item">
                            <?= __('Go to the shop, where you send this BITs;'); ?>
                        </li>
                        <li class="instruction__item">
                            <?= __('Go to the <strong>Deposit BIT > From other BitRewards shop;</strong>'); ?>
                        </li>
                        <li class="instruction__item">
                            <?= __('Paste this magic number to the input-form.'); ?>
                        </li>
                      </ol>
                    </div>

                    <div class="content-column content-column_layout_b">
                      <img class="" src="/loyalty/images/content/withdraw/image.png" srcset="/loyalty/images/content/withdraw/image@2x.png 2x">
                    </div>
                  </div>
                </li>

                <li class="form-fields-list__item">
                  <button type="button" class="button button_viewtype_modal c-primary-button js-close-modal">
                      <?= __('Done'); ?>
                  </button>
                </li>
              </ul>
            </fieldset>
          </div>
        </li>
      </ul>
    </div>
    <svg class="modal__close js-close-modal">
      <use xlink:href="#popup-close"></use>
    </svg>
  </div>

  <div class="modal modal_content_withdraw js-deposit-personal-modal" data-close-callback="DEPOSIT_PERSONAL_RESET">
    <h4 class="modal__title">
      <ul class="steps js-deposit-personal-steps">
        <li class="steps__item">
          <a class="steps__link c-step" data-step="1">
            1
          </a>
        </li>
        <li class="steps__item">
          <a class="steps__link c-step" data-step="2">
            2
          </a>
        </li>
      </ul>
    </h4>
    <div class="modal__body">
      <ul class="horizontal-slider js-deposit-shop-modal-steps js-deposit-personal-slider">
        <li class="horizontal-slider__item">
          <form method="POST" class="form form_content_withdraw js-deposit-personal-eth-form js-ajax-form" data-ajax-callback="DEPOSIT_PERSONAL_NEXT" action="<?= $partnerPage->partner->updateWalletAddressUrl; ?>">
            <fieldset class="form-fields-group">
              <legend class="form-fields-group__title">
                <?= __('Enter your Ethereum wallet address'); ?>
              </legend>
              <ul class="form-fields-list">
                <li class="form-fields-list__item">
                  <div class="field-composition">
                    <div class="field-composition__content">
                      <div class="form-field-box">
                        <input type="text" class="form-field form-field_type_text js-ethereum-wallet c-primary-input"
                               value="<?= $partnerPage->user->bitTokenSenderAddress; ?>"
                               name="ethereum_wallet" data-validate="true" data-required="true" data-validate-mode="eth" required>
                          <label class="form-field-label form-field-label_viewtype_float c-primary-label" for="ethereum_wallet">
                            <?= __('Put your ethereum wallet address here'); ?>
                          </label>
                      </div>
                      <small class="input-error js-input-error"></small>
                    </div>
                  </div>
                </li>

                <li class="form-fields-list__item">
                  <div class="warning c-incut">
                    <div class="warning__item">
                      <div class="warning__title">
                        <?= __('DO:'); ?>
                      </div>
                      <p class="warning__text">
                        <?= __('Your personal Ethereum wallet address: MetaMask, Mist, MyEtherWallet, Parity, Trust, imToken etc'); ?>
                      </p>
                    </div>

                    <div class="warning__item is-danger">
                      <div class="warning__title">
                        <?= __('DO NOT:'); ?>
                      </div>
                      <p class="warning__text">
                        <?= __("Exchange addresses, other service's addresses, other users' address."); ?>
                      </p>
                    </div>
                  </div>
                </li>

                <li class="form-fields-list__item form-fields-list__item_content_submit">
                  <button class="button button_viewtype_modal c-primary-button js-get-deposit-address" type="submit">
                    <?= __('Get the deposit address'); ?>
                  </button>
                </li>
              </ul>
            </fieldset>
          </form>
        </li>

        <li class="horizontal-slider__item">

          <div class="form form_content_withdraw">
            <fieldset class="form-fields-group">
              <legend class="form-fields-group__title">
                <?= __('Send BIT tokens with your Ethereum wallet'); ?>
              </legend>
              <ul class="form-fields-list">
                <li class="form-fields-list__item">
                  <div class="field-composition">
                    <div class="field-composition__title">
                      <label class="form-field-label c-primary-label" for="deposit_from">
                        <?= __('From'); ?>
                      </label>
                    </div>
                    <div class="field-composition__content">
                      <div class="form-field-box">
                        <input
                          type="text"
                          class="form-field form-field_type_text js-deposit-from c-primary-input"
                          name="deposit_from"
                          value=""
                          disabled
                        >
                      </div>
                      <small class="input-error js-input-error"></small>
                    </div>
                  </div>
                </li>

                <li class="form-fields-list__item">
                  <div class="field-composition">
                    <div class="field-composition__title">
                      <label class="form-field-label c-primary-label" for="deposit_to">
                        <?= __('To'); ?>
                      </label>
                    </div>
                    <div class="field-composition__content">
                      <div class="form-field-box">
                        <input
                          type="text"
                          class="form-field form-field_type_text form-field_content_clipboard js-deposit-to c-primary-input"
                          name="deposit_to"
                          id="deposit_to"
                          data-validate="true"
                          data-required="true"
                          data-validate-mode="eth"
                          value="<?= $partnerPage->partner->ethAddress; ?>"
                          readonly
                          required
                        >
                        <svg class="clipboard c-primary-fill js-clipboard js-tooltip" data-tooltip-text="<?= __('Copy address'); ?>" data-clipboard-target="#deposit_to">
                          <use xlink:href="#clipboard"></use>
                        </svg>
                        <svg class="done c-primary-fill js-copy-done js-tooltip" data-tooltip-text="<?= __('Copied!'); ?>">
                          <use xlink:href="#done"></use>
                        </svg>
                      </div>
                      <small class="input-error js-input-error"></small>
                    </div>
                  </div>
                </li>

                <li class="form-fields-list__item">
                  <div class="warning">
                    <div class="warning__title">
                      <?= __('Attention'); ?>
                    </div>
                    <p class="warning__text i i_content_eth-address">
                      <?= __("1. <strong>Send only BIT.</strong> Don't send ETH or other tokens."); ?><br>
                      <?= __("2. Send BIT only from <strong class='i__text js-deposit-eth'></strong>"); ?>.<br>
                      <?= __("3. Check the <a class='link link_viewtype_standard' href='#''>instructions</a> for your wallet."); ?>
                    </p>
                  </div>
                </li>

                <li class="form-fields-list__item form-fields-list__item_content_submit">
                  <button type="button" class="button button_viewtype_modal c-primary-button js-close-modal">
                    <?= __('Done'); ?>
                  </button>
                </li>
              </ul>
            </fieldset>
          </div>
        </li>
      </ul>
    </div>
    <svg class="modal__close js-close-modal">
      <use xlink:href="#popup-close"></use>
    </svg>
  </div>

  <div class="modal modal_content_withdraw js-deposit-shop-modal" data-close-callback="DEPOSIT_SHOP_RESET">
    <h4 class="modal__title">
      <ul class="steps js-deposit-shop-steps is-active">
        <li class="steps__item">
          <a class="steps__link c-step" data-step="1">
            1
          </a>
        </li>
        <li class="steps__item">
          <a class="steps__link c-step" data-step="2">
            2
          </a>
        </li>
      </ul>
    </h4>
    <div class="modal__body">
      <ul class="horizontal-slider js-deposit-shop-slider">
        <li class="horizontal-slider__item">
          <div class="form form_content_withdraw">
            <fieldset class="form-fields-group">
              <legend class="form-fields-group__title">
                <?= __("Fill in the address input sender's form with this Ethereum address"); ?>
              </legend>
              <ul class="form-fields-list">
                <li class="form-fields-list__item">
                  <dl class="field-composition">
                    <dd class="field-composition__content">
                      <div class="form-field-box">
                        <input
                          type="text"
                          class="form-field form-field_type_text form-field_content_clipboard c-primary-input"
                          name="deposit_eth"
                          id="deposit_eth"
                          value="<?= $partnerPage->partner->ethAddress; ?>"
                          readonly
                        >
                        <svg class="clipboard c-primary-fill js-clipboard js-tooltip" data-tooltip-text="<?= __('Copy address'); ?>" data-clipboard-target="#deposit_eth">
                          <use xlink:href="#clipboard"></use>
                        </svg>
                        <svg class="done c-primary-fill js-copy-done js-tooltip" data-tooltip-text="<?= __('Copied!'); ?>">
                          <use xlink:href="#done"></use>
                        </svg>
                      </div>
                    </dd>
                  </dl>
                </li>

                <li class="form-fields-list__item">
                  <div class="content-columns content-columns_content_instruction">
                    <div class="content-column content-column_layout_a">
                      <ol class="instruction">
                        <li class="instruction__item">
                          <?= __('Open the parallel tab and go to the store loyalty program, from where you want to transfer your BIT.'); ?>
                        </li>
                        <li class="instruction__item">
                          <?= __('Go to the section Withdraw BIT and push the Withdraw BIT button.'); ?>
                        </li>
                        <li class="instruction__item">
                          <?= __('Insert copied ethereum address in the corresponding field and press "Send amount" button'); ?>
                        </li>
                        <li class="instruction__item">
                          <?= __('After withdraw amount setting you will get transaction ID. Copy it in the sender store.'); ?>
                        </li>
                      </ol>
                    </div>

                    <div class="content-column content-column_layout_b">
                      <img class="" src="/loyalty/images/content/deposit/image.png" srcset="/loyalty/images/content/deposit/image@2x.png 2x">
                    </div>
                  </div>
                </li>

                <li class="form-fields-list__item form-fields-list__item_content_submit">
                  <button class="button button_viewtype_modal c-primary-button js-deposit-shop-next" type="button">
                      <?= __('Fill in the transaction ID'); ?>
                  </button>
                </li>
              </ul>
            </fieldset>
          </div>
        </li>

        <li class="horizontal-slider__item">

          <form action="<?=$partnerPage->partner->clientRewardConfirmDepositUrl; ?>" method="post" class="form form_content_withdraw js-ajax-form" data-ajax-callback="DEPOSIT_SHOP_SUCCESS">
            <fieldset class="form-fields-group">
              <legend class="form-fields-group__title">
                <?= __('Fill in this input form with the transaction ID from sender'); ?>
              </legend>
              <ul class="form-fields-list">
                <li class="form-fields-list__item">
                  <div class="field-composition">
                    <div class="field-composition__content">
                      <div class="form-field-box">
                        <input type="text" class="form-field form-field_type_text js-deposit-magic c-primary-input" name="deposit_magic" data-validate="true" data-required="true" required>
                          <label class="form-field-label form-field-label_viewtype_float c-primary-label" for="deposit_magic">
                            <?= __('Paste the transaction ID here'); ?>
                          </label>
                      </div>
                      <small class="input-error js-input-error"></small>
                    </div>
                  </div>
                </li>

                <li class="form-fields-list__item">
                  <div class="content-columns content-columns_content_instruction">
                    <div class="content-column content-column_layout_a">
                      <p>
                        <?= __("The transaction ID is necessary for payment's identification. You can get it in the store, from where you've sent BIT to this store."); ?>
                      </p>
                      <br>
                      <a class="link link_viewtype_standard" href="#">
                        <?= __("Didn't get the transaction ID?"); ?>
                      </a>
                    </div>

                    <div class="content-column content-column_layout_b">
                      <img class="" src="/loyalty/images/content/deposit/image-2.png" srcset="/loyalty/images/content/deposit/image-2@2x.png 2x">
                    </div>
                  </div>
                </li>

                <li class="form-fields-list__item form-fields-list__item_content_submit">
                <button type="submit" class="button button_viewtype_modal c-primary-button js-loader">
                    <?= __('Done'); ?>
                  </button>
                </li>
              </ul>
            </fieldset>
          </form>
        </li>
      </ul>
    </div>
    <svg class="modal__close js-close-modal">
      <use xlink:href="#popup-close"></use>
    </svg>
  </div>

  <div class="modal modal_content_withdraw js-exchange-modal" data-close-callback="EXCHANGE_MODAL_RESET">
      <h4 class="modal__title">
        <ul class="steps js-exchange-steps">
          <li class="steps__item">
            <a class="steps__link c-step" data-step="1">
              1
            </a>
          </li>
          <li class="steps__item">
            <a class="steps__link c-step" data-step="2">
              2
            </a>
          </li>
        </ul>
      </h4>
      <div class="modal__body">
        <ul class="horizontal-slider js-exchange-slider">
          <li class="horizontal-slider__item">
            <form method="POST" class="form form_content_withdraw js-exchange-form js-ajax-form" data-ajax-callback="EXCHANGE_MODAL_NEXT" action="<?=$partnerPage->viewData->updateEthWalletAddressUrl; ?>">
              <fieldset class="form-fields-group">
                <legend class="form-fields-group__title">
                  <?= __('Enter your Ethereum wallet address'); ?>
                </legend>
                <ul class="form-fields-list">
                  <li class="form-fields-list__item">
                    <dl class="field-composition">
                      <dd class="field-composition__content">
                        <div class="form-field-box">
                          <input
                            type="text"
                            class="form-field form-field_type_text c-primary-input js-exchange-eth"
                            name="ethereum_wallet"
                            id="exchange_eth"
                            value="<?= $partnerPage->user->ethSenderAddress; ?>"
                            data-validate="true"
                            data-required="true"
                            data-validate-mode="eth"
                          >
                        </div>
                      </dd>
                    </dl>
                  </li>

                  <li class="form-fields-list__item">
                    <div class="warning c-incut">
                      <div class="warning__item">
                        <div class="warning__title">
                          <?= __('DO:'); ?>
                        </div>
                        <p class="warning__text">
                          <?= __('Your personal Ethereum wallet address: MetaMask, Mist, MyEtherWallet, Parity, Trust, imToken etc'); ?>
                        </p>
                      </div>

                      <div class="warning__item is-danger">
                        <div class="warning__title">
                          <?= __('DO NOT:'); ?>
                        </div>
                        <p class="warning__text">
                          <?= __("Exchange addresses, other service's addresses, other user's addresses."); ?>
                        </p>
                      </div>
                    </div>
                  </li>

                  <li class="form-fields-list__item form-fields-list__item_content_submit">
                    <button class="button button_viewtype_modal c-primary-button" type="submit">
                        <?= __('Next'); ?>
                    </button>
                  </li>
                </ul>
              </fieldset>
            </form>
          </li>

          <li class="horizontal-slider__item">

            <form action="" method="post" class="form form_content_withdraw js-ajax-form" data-ajax-callback="EXCHANGE_SUCCESS">
              <fieldset class="form-fields-group">
                <legend class="form-fields-group__title">
                  <?= __('Sent ETH to a BIT wallet'); ?>
                </legend>
                <ul class="form-fields-list">
                  <li class="form-fields-list__item">
                    <ul class="exchange-rates">
                      <li class="exchange-rates__item">
                        <span class="exchange-rates__item-text">1 BIT = <?=$partnerPage->viewData->bitToEthExchangeRate; ?> ETH</span>
                      </li>

                      <li class="exchange-rates__item">
                        <span class="exchange-rates__item-text">1 ETH = <?=$partnerPage->viewData->ethToBitExchangeRate; ?> BIT</span>
                      </li>
                    </ul>
                    <br>
                    <p class="form-remark form-remark_viewtype_small">
                      <?= __('The exchange rate is approximate. Exchange is made at the rate at the moment of reciept ETH on exchange wallet'); ?>
                    </p>
                  </li>

                  <li class="form-fields-list__item">
                    <div class="field-composition">
                      <div class="field-composition__title">
                        <label class="form-field-label c-primary-label" for="exchange_from">
                          <?= __('From'); ?>
                        </label>
                      </div>
                      <div class="field-composition__content">
                        <div class="form-field-box">
                          <input
                            type="text"
                            class="form-field form-field_type_text js-exchange-from c-primary-input"
                            name="exchange_from"
                            value=""
                            disabled
                          >
                        </div>
                        <small class="input-error js-input-error"></small>
                      </div>
                    </div>
                  </li>

                  <li class="form-fields-list__item">
                    <div class="field-composition">
                      <div class="field-composition__title">
                        <label class="form-field-label c-primary-label" for="exchange_to">
                          <?= __('To'); ?>
                        </label>
                      </div>
                      <div class="field-composition__content">
                        <div class="form-field-box">
                          <input
                            type="text"
                            class="form-field form-field_type_text form-field_content_clipboard js-exchange-to c-primary-input"
                            name="exchange_to"
                            id="exchange_to"
                            data-validate="true"
                            data-required="true"
                            data-validate-mode="eth"
                            value="<?= config('treasury.exchange_address'); ?>"
                            readonly
                            required
                          >
                          <svg class="clipboard c-primary-fill js-clipboard js-tooltip" data-tooltip-text="<?= __('Copy address'); ?>" data-clipboard-target="#exchange_to">
                            <use xlink:href="#clipboard"></use>
                          </svg>
                          <svg class="done c-primary-fill js-copy-done js-tooltip" data-tooltip-text="<?= __('Copied!'); ?>">
                            <use xlink:href="#done"></use>
                          </svg>
                        </div>
                        <small class="input-error js-input-error"></small>
                      </div>
                    </div>
                  </li>

                  <li class="form-fields-list__item form-fields-list__item_content_submit">
                  <button type="button" class="button button_viewtype_modal c-primary-button js-close-modal">
                      <?= __('I sent'); ?>
                    </button>
                  </li>
                </ul>
              </fieldset>
            </form>
          </li>
        </ul>
      </div>
      <svg class="modal__close js-close-modal">
        <use xlink:href="#popup-close"></use>
      </svg>
    </div>
  <?php endif; ?>

<?php if (Auth::user()) :?>
    <div class="modal modal_content_change-password js-change-password-modal" data-close-callback="CHANGE_PASSWORD_RESET">
      <div class="modal__body">
        <ul class="horizontal-slider js-change-password-modal-slider">
          <li class="horizontal-slider__item">
            <form class="form form_content_change-password js-ajax-form" action="" data-ajax-callback="CHANGE_PASSWORD_OLD" method="POST">
              <fieldset class="form-fields-group">
                <h2 class="modal__title">
                  <?= __('Enter old password'); ?>
                </h2>
                <ul class="form-fields-list">
                  <li class="form-fields-list__item">
                    <ul class="form-fields-groupbox form-fields-groupbox_content_auth-form">
                      <li class="form-fields-groupbox__section">
                        <div class="field-composition">
                          <div class="field-composition__content">
                            <div class="form-field-box">
                              <input type="password" class="form-field form-field_type_text c-primary-input" name="old_password" data-validate="true" data-required="true" required>
                                <label class="form-field-label form-field-label_viewtype_float c-primary-label" for="old_password">
                                  <?= __('Old password'); ?>
                                </label>
                            </div>
                            <small class="input-error js-input-error"></small>
                          </div>
                        </div>
                      </li>

                      <li class="form-fields-groupbox__section">
                        <button class="button button_type_block button_viewtype_primary c-primary-button" type="button">
                          <span class="button__text">
                            <?= __('Next'); ?>
                          </span>
                        </button>
                      </li>
                    </ul>
                  </li>

                  <li class="form-fields-list__item">
                    <button type="button" class="link link_viewtype_pseudo c-pseudo-link js-forgot-password-button">
                      <?= __('I forgot my password'); ?>
                    </button>
                  </li>
                </ul>
              </fieldset>
            </form>
          </li>

          <li class="horizontal-slider__item">
            <form class="form form_content_change-password js-ajax-form" action="" method="POST">
              <fieldset class="form-fields-group">
                <h2 class="modal__title">
                  <?= __('Enter new password'); ?>
                </h2>
                <ul class="form-fields-list">
                  <li class="form-fields-list__item">
                    <ul class="form-fields-groupbox form-fields-groupbox_content_auth-form">
                      <li class="form-fields-groupbox__section">
                        <div class="field-composition">
                          <div class="field-composition__content">
                            <div class="form-field-box">
                              <input type="password" class="form-field form-field_type_text c-primary-input" name="new_password" data-validate="true" data-required="true" required>
                                <label class="form-field-label form-field-label_viewtype_float c-primary-label" for="new_password">
                                  <?= __('New password'); ?>
                                </label>
                            </div>
                            <small class="input-error js-input-error"></small>
                          </div>
                        </div>
                      </li>

                      <li class="form-fields-groupbox__section">
                        <div class="field-composition">
                          <div class="field-composition__content">
                            <div class="form-field-box">
                              <input type="password" class="form-field form-field_type_text c-primary-input" name="repeat_new_password" data-validate="true" data-required="true" required>
                                <label class="form-field-label form-field-label_viewtype_float c-primary-label" for="repeat_new_password">
                                  <?= __('Repeat new password'); ?>
                                </label>
                            </div>
                            <small class="input-error js-input-error"></small>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </li>

                  <li class="form-fields-list__item form-fields-list__item_content_submit">
                    <button class="button button_type_block button_viewtype_primary c-primary-button" type="submit">
                      <span class="button__text">
                        <?= __('Next'); ?>
                      </span>
                    </button>
                  </li>
                </ul>
              </fieldset>
            </form>
          </li>

          <li class="horizontal-slider__item">
            <div class="form-success-send form-success-send_viewtype_small">
              <svg class="form-success-send__icon c-primary-fill">
                <use xlink:href="#ok2"></use>
              </svg>
              <div class="form-success-send__title">
                <?= __('The password has been changed!'); ?>
              </div>
            </div>
          </li>
        </ul>
      </div>

      <div class="forgot-password js-forgot-password">
        <div class="forgot-password__in">
          <form class="form form_content_change-password js-ajax-form" action="" data-ajax-callback="RESET_PASSWORD_SUCCESS" method="POST">
            <fieldset class="form-fields-group">
              <h2 class="modal__title">
                <?= __("New password sent to <span class='js-forgot-email'>1@mail.com</span>"); ?>
              </h2>
              <ul class="form-fields-list">
                <li class="form-fields-list__item">
                  <p class="form-remark">
                    <?= __("We’ve sent the new password to <span class='js-forgot-email'>1@mail.com</span>. Check your mail, copy and paste the new password in the input form to change password."); ?>
                  </p>
                </li>

                <li class="form-fields-list__item">
                  <ul class="form-fields-groupbox form-fields-groupbox_content_auth-form">
                    <li class="form-fields-groupbox__section">
                      <div class="field-composition">
                        <div class="field-composition__content">
                          <div class="form-field-box">
                            <input type="password" class="form-field form-field_type_text c-primary-input" name="reset_new_password" data-validate="true" data-required="true" required>
                              <label class="form-field-label form-field-label_viewtype_float c-primary-label" for="reset_new_password">
                                <?= __('New password'); ?>
                              </label>
                          </div>
                          <small class="input-error js-input-error"></small>
                        </div>
                      </div>
                    </li>

                    <li class="form-fields-groupbox__section">
                      <button class="button button_type_block button_viewtype_primary c-primary-button" type="submit">
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

        <button class="back-button modal__back js-modal-back" type="button" aria-label="<?= __('Back'); ?>">
          <svg class="back-button__icon">
            <use xlink:href="#back-arrow"></use>
          </svg>
        </button>
      </div>
      <svg class="modal__close js-close-modal">
        <use xlink:href="#popup-close"></use>
      </svg>
    </div>

    <div class="modal modal_content_confirm js-confirm-email-modal" data-close-callback="CONFIRM_EMAIL_RESET">
      <div class="modal__body">
        <ul class="horizontal-slider horizontal-slider_content_confirm-email js-confirm-email-slider">
          <li class="horizontal-slider__item">
            <form class="form form_content_confirm js-ajax-form" method="POST" action="<?=$partnerPage->partner->confirmEmailUrl; ?>" data-ajax-callback="CONFIRM_EMAIL_BIND">
              <fieldset class="form-fields-group">
                <h2 class="modal__title">
                  <?= __('Enter your email to bind it'); ?>
                </h2>
                <ul class="form-fields-list">
                  <li class="form-fields-list__item">
                    <ul class="form-fields-groupbox form-fields-groupbox_content_auth-form">
                      <li class="form-fields-groupbox__section">
                        <div class="field-composition">
                          <div class="field-composition__content">
                            <div class="form-field-box">
                              <input type="text" class="form-field form-field_type_text c-primary-input js-bind-email" name="email" data-validate="true" data-required="true" data-validate-mode="email" required>
                                <label class="form-field-label form-field-label_viewtype_float c-primary-label" for="bind_email">
                                  <?= __('Your email'); ?>
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
          </li>

          <li class="horizontal-slider__item horizontal-slider__item_content_bind">
            <form class="form form_content_confirm js-ajax-form" action="" data-ajax-callback="CONFIRM_EMAIL_SEND_BIND" method="POST">
              <input type="hidden" name="email" class="js-hidden-bind-email">
              <fieldset class="form-fields-group">
                <h2 class="modal__title">
                  <?= __("<strong class='js-confirm-email'></strong> is already binded to another account"); ?>
                </h2>
              </fieldset>
              <ul class="form-fields-list">
                <li class="form-fields-list__item">
                  <div class="person person_viewtype_badge">
                    <div class="person__avatar">
                      <img src="/loyalty/images/content/avatar.png" width="36" height="36">
                    </div>
                    <div class="person__name">
                      Constantin Constantinov
                    </div>
                  </div>
                </li>

                <li class="form-fields-list__item">
                  <span class="form-remark">
                    <?= __('Bind them if it is you?'); ?>
                  </span>
                </li>

                <li class="form-fields-list__item">
                  <ul class="form-fields-groupbox form-fields-groupbox_content_action">
                    <li class="form-fields-groupbox__section">
                      <button type="button" class="button button_viewtype_secondary button_type_block button_content_back js-go-back">
                        <svg class="button__icon">
                          <use xlink:href="#arrow-left"></use>
                        </svg>
                        <span class="button__text">
                          <?= __('No it’s not me'); ?>
                        </span>
                      </button>
                    </li>

                    <li class="form-fields-groupbox__section">
                      <button type="submit" class="button button_viewtype_primary button_type_block c-primary-button js-loader">
                        <span class="button__text">
                          <?= __('Bind them'); ?>
                        </span>
                      </button>
                    </li>
                  </ul>
                </li>
              </ul>
            </form>
          </li>

          <li class="horizontal-slider__item">
            <form class="form form_content_confirm js-ajax-form" action="<?=$partnerPage->partner->addEmailUrl; ?>" data-ajax-callback="CONFIRM_EMAIL_SEND_CODE" method="POST">
              <input type="hidden" name="email" class="js-hidden-bind-email">
              <fieldset class="form-fields-group">
                <h2 class="modal__title">
                  <?= __('Confirm email'); ?>
                </h2>
                <ul class="form-fields-list">
                  <li class="form-fields-list__item">
                    <p class="form-remark">
                      <?= __("We’ve sent the confirmation request to <strong class='js-confirm-email'></strong>. Check your mail and go through the link or put the code in the input form to bind accounts."); ?>
                    </p>
                  </li>

                  <li class="form-fields-list__item">
                    <ul class="form-fields-groupbox form-fields-groupbox_content_auth-form">
                      <li class="form-fields-groupbox__section">
                        <div class="field-composition">
                          <div class="field-composition__content">
                            <div class="form-field-box">
                              <input type="text" class="form-field form-field_type_text c-primary-input js-confirm-code" name="confirm_code" data-validate="true" data-required="true" required>
                                <label class="form-field-label form-field-label_viewtype_float c-primary-label" for="confirm_code">
                                  <?= __('Сonfirmation сode'); ?>
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
          </li>

          <li class="horizontal-slider__item horizontal-slider__item_content_bind">
            <form class="form form_content_confirm js-ajax-form" action="" data-ajax-callback="CONFIRM_EMAIL_UPDATE" method="POST">
              <fieldset class="form-fields-group">
                <h2 class="modal__title">
                  <?= __('Choose actual personal info'); ?>
                </h2>
                <ul class="form-fields-list">
                  <li class="form-fields-list__item">
                    <ul class="form-fields-groupbox form-fields-groupbox_content_tile">
                      <li class="form-fields-groupbox__section">
                        <div class="tile-row">
                          <label class="tile">
                            <input type="radio" name="update_img" class="tile__input c-tile" value="/loyalty/images/content/avatar.png" checked>
                            <span class="tile__pseudo c-tile-pseudo" aria-hidden="true"></span>
                            <span class="tile__decor c-tile-decor" aria-hidden="true"></span>
                            <dl class="tile__content def">
                              <dt class="def__key">
                                <?= __('Image'); ?>
                              </dt>
                              <dd class="def__value">
                                <img src="/loyalty/images/content/avatar.png" width="66" height="66">
                              </dd>
                            </dl>
                          </label>
                        </div>

                        <div class="tile-row">
                          <label class="tile">
                            <input type="radio" name="update_name" class="tile__input c-tile" value="Константин" checked>
                            <span class="tile__pseudo c-tile-pseudo" aria-hidden="true"></span>
                            <span class="tile__decor c-tile-decor" aria-hidden="true"></span>
                            <dl class="tile__content def">
                              <dt class="def__key">
                                <?= __('First name'); ?>
                              </dt>
                              <dd class="def__value">
                                {{ \Auth::user()->getFirstName() }}
                              </dd>
                            </dl>
                          </label>
                        </div>

                        <div class="tile-row">
                          <label class="tile">
                            <input type="radio" name="update_second_name" class="tile__input c-tile" value="Константинов" checked>
                            <span class="tile__pseudo c-tile-pseudo" aria-hidden="true"></span>
                            <span class="tile__decor c-tile-decor" aria-hidden="true"></span>
                            <dl class="tile__content def">
                              <dt class="def__key">
                                <?= __('Second name'); ?>
                              </dt>
                              <dd class="def__value">
                                {{ \Auth::user()->getSecondName() }}
                              </dd>
                            </dl>
                          </label>
                        </div>
                      </li>

                      <li class="form-fields-groupbox__section">
                        <div class="tile-row">
                          <label class="tile c-tile">
                            <input type="radio" name="update_img" class="tile__input c-tile" value="/loyalty/images/content/avatar.png">
                            <span class="tile__pseudo c-tile-pseudo" aria-hidden="true"></span>
                            <span class="tile__decor c-tile-decor" aria-hidden="true"></span>
                            <dl class="tile__content def">
                              <dt class="def__key">
                                <?= __('Image'); ?>
                              </dt>
                              <dd class="def__value">
                                <img src="/loyalty/images/content/avatar.png" width="66" height="66">
                              </dd>
                            </dl>
                          </label>
                        </div>

                        <div class="tile-row">
                          <label class="tile">
                            <input type="radio" name="update_name" class="tile__input c-tile" value="Constantin">
                            <span class="tile__pseudo c-tile-pseudo" aria-hidden="true"></span>
                            <span class="tile__decor c-tile-decor" aria-hidden="true"></span>
                            <dl class="tile__content def">
                              <dt class="def__key">
                                <?= __('First name'); ?>
                              </dt>
                              <dd class="def__value">
                                Constantin
                              </dd>
                            </dl>
                          </label>
                        </div>

                        <div class="tile-row">
                          <label class="tile">
                            <input type="radio" name="update_second_name" class="tile__input c-tile" value="Constantinov">
                            <span class="tile__pseudo c-tile-pseudo" aria-hidden="true"></span>
                            <span class="tile__decor c-tile-decor" aria-hidden="true"></span>
                            <dl class="tile__content def">
                              <dt class="def__key">
                                <?= __('Second name'); ?>
                              </dt>
                              <dd class="def__value">
                                Constantinov
                              </dd>
                            </dl>
                          </label>
                        </div>
                      </li>
                    </ul>
                  </li>

                  <li class="form-fields-list__item">
                    <button type="submit" class="button button_viewtype_modal button_type_block c-primary-button js-loader">
                      <span class="button__text">
                        <?= __('Update'); ?>
                      </span>
                    </button>
                  </li>
                </ul>
              </fieldset>
            </form>
          </li>

          <li class="horizontal-slider__item">
            <div class="form-success-send form-success-send_viewtype_small">
              <svg class="form-success-send__icon c-primary-fill">
                <use xlink:href="#ok2"></use>
              </svg>
              <div class="form-success-send__title">
                <?= __("Your email <strong class='js-confirm-email'></strong> <br>was succesfully binded to your account!"); ?>
              </div>
            </div>
          </li>
        </ul>

      </div>
      <button class="back-button modal__back js-modal-back" type="button" aria-label="<?= __('Back'); ?>">
        <svg class="back-button__icon">
          <use xlink:href="#back-arrow"></use>
        </svg>
      </button>
      <svg class="modal__close js-close-modal">
        <use xlink:href="#popup-close"></use>
      </svg>
    </div>

    <div class="modal modal_content_confirm js-confirm-phone-modal" data-close-callback="CONFIRM_PHONE_RESET">
      <div class="modal__body">
        <ul class="horizontal-slider js-reset-password-modal-slider js-confirm-phone-slider">
          <li class="horizontal-slider__item">
            <form class="form form_content_confirm js-ajax-form" action="<?=$partnerPage->partner->confirmPhoneUrl; ?>" data-ajax-callback="CONFIRM_PHONE_BIND" method="POST">
              <fieldset class="form-fields-group">
                <h2 class="modal__title">
                  <?= __('Enter your phone to bind it'); ?>
                </h2>
                <ul class="form-fields-list">
                  <li class="form-fields-list__item">
                    <ul class="form-fields-groupbox form-fields-groupbox_content_auth-form">
                      <li class="form-fields-groupbox__section">
                        <div class="field-composition">
                          <div class="field-composition__content">
                            <div class="form-field-box">
                              <input type="text" class="form-field form-field_type_text c-primary-input js-bind-phone" name="phone" data-validate="true" data-required="true" required>
                                <label class="form-field-label form-field-label_viewtype_float c-primary-label js-bind-phone" for="phone">
                                  <?= __('Your phone'); ?>
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
          </li>

          <li class="horizontal-slider__item">
            <form class="form form_content_confirm js-ajax-form" action="<?=$partnerPage->partner->addPhoneUrl; ?>" data-ajax-callback="CONFIRM_PHONE_SEND_CODE" method="POST">
              <input type="hidden" name="phone" class="js-hidden-bind-phone">
              <fieldset class="form-fields-group">
                <h2 class="modal__title">
                  <?= __('Confirm phone number'); ?>
                </h2>
                <ul class="form-fields-list">
                  <li class="form-fields-list__item">
                    <p class="form-remark">
                      <?= __('We’ve sent the SMS with confirmation code. Enter it in the input form to bind accounts.'); ?>
                    </p>
                  </li>

                  <li class="form-fields-list__item">
                    <ul class="form-fields-groupbox form-fields-groupbox_content_auth-form">
                      <li class="form-fields-groupbox__section">
                        <div class="field-composition">
                          <div class="field-composition__content">
                            <div class="form-field-box">
                              <input type="text" class="form-field form-field_type_text c-primary-input js-confirm-sms" name="confirm_sms" data-validate="true" data-required="true" required>
                                <label class="form-field-label form-field-label_viewtype_float c-primary-label" for="confirm_sms">
                                  <?= __('SMS code'); ?>
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
          </li>

          <li class="horizontal-slider__item">
            <div class="form-success-send form-success-send_viewtype_small">
              <svg class="form-success-send__icon c-primary-fill">
                <use xlink:href="#ok2"></use>
              </svg>
              <div class="form-success-send__title">
                <?= __("Your phone <strong class='js-confirm-phone'></strong> <br> was succesfully binded to your account!"); ?>
              </div>
            </div>
          </li>
        </ul>
      </div>
      <button class="back-button modal__back js-modal-back" type="button" aria-label="<?= __('Back'); ?>">
        <svg class="back-button__icon">
          <use xlink:href="#back-arrow"></use>
        </svg>
      </button>
      <svg class="modal__close js-close-modal">
        <use xlink:href="#popup-close"></use>
      </svg>
    </div>
<?php endif; ?>

<?php if ($partnerPage->partner->isFiatReferrerEnabled) {
                      ?>
  <?php if (!$partnerPage->partner->isHowItWorksHidden): ?>
  <div class="modal modal_content_how-it-work js-how-it-works" data-before-open-callback="HOW_IT_WORK_MODAL_OPEN" data-close-callback="HOW_IT_WORKS_MODAL_CLOSE">
      <h4 class="modal__title">
        <?= __('How it works'); ?>
      </h4>
      <div class="modal__body">
        <div class="how-it-work">
          <ul class="how-it-work__list">
            <li class="how-it-work__item">
              <div class="story story_content_how-it-work">
                <div class="story__thumbnail">
                  <svg class="story__image story__image_step_1">
                    <use xlink:href="#instr1"></use>
                  </svg>
                </div>

                <p class="story__text">
                  <?= __('Copy the referral link to the product page.'); ?>
                </p>

                <div class="story__action">
                  <button type="button" class="link link_viewtype_standard link_content_tooltip i i_content_bold" data-text="<?= htmlspecialchars(\HCustomizations::howItWorksFirstStepTipMessage($partnerPage->viewData->partner)); ?>">
                    <span class="i__text c-primary-color"><?= __('Read more'); ?></span>
                  </button>
                </div>
              </div>
            </li>

            <li class="how-it-work__item how-it-work__item_content_arrow" aria-hidden="true">
              <svg class="how-it-work__arrow">
                <use xlink:href="#arrow"></use>
              </svg>
            </li>

            <li class="how-it-work__item">
              <div class="story story_content_how-it-work">
                <div class="story__thumbnail">
                  <svg class="story__image story__image_step_2">
                    <use xlink:href="#instr2"></use>
                  </svg>
                </div>

                <p class="story__text">
                  <?= __('Share the copied link and earn.'); ?>
                </p>

                <div class="story__action">
                  <button type="button" class="link link_viewtype_standard i i_content_bold link_content_tooltip" data-text="<?= htmlspecialchars(\HCustomizations::howItWorksSecondStepTipMessage($partnerPage->viewData->partner)); ?>">
                    <span class="i__text c-primary-color"><?= __('Read more'); ?></span>
                  </button>
                </div>
              </div>
            </li>

            <?php if (!$partnerPage->viewData->isWithdrawDisabled): ?>
              <li class="how-it-work__item how-it-work__item_content_arrow" aria-hidden="true">
                <svg class="how-it-work__arrow">
                  <use xlink:href="#arrow"></use>
                </svg>
              </li>


              <li class="how-it-work__item">
                <div class="story story_content_how-it-work">
                  <div class="story__thumbnail">
                    <svg class="story__image story__image_step_3">
                      <use xlink:href="#instr3"></use>
                    </svg>
                  </div>

                  <p class="story__text">
                    <?= __('Withdraw to your Visa and Mastercard.'); ?>
                  </p>

                  <div class="story__action">
                    <button type="button" class="link link_viewtype_standard i i_content_bold link_content_tooltip" data-text="<?= htmlspecialchars(\HCustomizations::howItWorksThirdStepTipMessage($partnerPage->viewData->partner)); ?>">
                      <span class="i__text c-primary-color"><?= __('Read more'); ?></span>
                    </button>
                  </div>
                </div>
              </li>
            <?php endif; ?>
          </ul>

          <div class="how-it-work__action">
            <button type="button" class="button button_viewtype_primary button_type_block c-primary-button js-close-modal js-understand-button">
              <span class="button__text">
                <?= __("Got it. Let's begin!"); ?>
              </span>
            </button>
          </div>
        </div>
      </div>
      <svg class="modal__close js-close-modal js-close-button">
        <use xlink:href="#popup-close"></use>
      </svg>
    </div>
    <?php endif; ?>
  <?php
                  } else {
                      ?>

    <?php if (!$partnerPage->partner->isGradedPercentRewardModeEnabled): ?>
    <div class="modal modal_content_how-it-work js-how-it-works">
      <h4 class="modal__title">
        <?= __('How it works'); ?>
      </h4>
      <div class="modal__body">
        <div class="how-it-work">
          <ul class="how-it-work__list">
            <li class="how-it-work__item">
              <div class="story story_content_how-it-work">
                <div class="story__thumbnail">
                  <svg class="story__image story__image_step_1">
                    <use xlink:href="#instr4"></use>
                  </svg>
                </div>

                <p class="story__text">
                  <?= __('Earn points in the "Earn points" section. '); ?>
                </p>

                <div class="story__action">
                  <button type="button" class="link link_viewtype_standard link_content_tooltip i i_content_bold" data-text="<?= htmlspecialchars(__("You can earn points for purchases, friends' purchases and social activity.")); ?>">
                    <span class="i__text c-primary-color"><?= __('Read more'); ?></span>
                  </button>
                </div>
              </div>
            </li>

            <li class="how-it-work__item how-it-work__item_content_arrow" aria-hidden="true">
              <svg class="how-it-work__arrow">
                <use xlink:href="#arrow"></use>
              </svg>
            </li>

            <li class="how-it-work__item">
              <div class="story story_content_how-it-work">
                <div class="story__thumbnail">
                  <svg class="story__image story__image_step_2">
                    <use xlink:href="#instr2"></use>
                  </svg>
                </div>

                <p class="story__text">
                  <?= __('Invite friends and earn points from their purchases.'); ?>
                </p>

                <div class="story__action">
                  <button type="button" class="link link_viewtype_standard i i_content_bold link_content_tooltip" data-text="<?= htmlspecialchars(__('In the “Invite friends” section you can get a referral link that you can share with your friends and earn cashback for their purchases.')); ?>">
                    <span class="i__text c-primary-color"><?= __('Read more'); ?></span>
                  </button>
                </div>
              </div>
            </li>

            <li class="how-it-work__item how-it-work__item_content_arrow" aria-hidden="true">
              <svg class="how-it-work__arrow">
                <use xlink:href="#arrow"></use>
              </svg>
            </li>

            <li class="how-it-work__item">
              <div class="story story_content_how-it-work">
                <div class="story__thumbnail">
                  <svg class="story__image story__image_step_3">
                    <use xlink:href="#instr5"></use>
                  </svg>
                </div>

                <p class="story__text">
                  <?= __('Redeem points for rewards and purchases in the "Redeem points" section.'); ?>
                </p>

                <div class="story__action">
                  <button type="button" class="link link_viewtype_standard i i_content_bold link_content_tooltip" data-text="<?= htmlspecialchars(__('Redeem points for discounts, coupons, products or services. Offers are constantly updated!')); ?>">
                    <span class="i__text c-primary-color"><?= __('Read more'); ?></span>
                  </button>
                </div>
              </div>
            </li>
          </ul>

          <div class="how-it-work__action">
            <button type="button" class="button button_viewtype_primary button_type_block c-primary-button js-close-modal js-understand-button">
              <span class="button__text">
                <?= __("Got it. Let's begin!"); ?>
              </span>
            </button>
          </div>
        </div>
      </div>
      <svg class="modal__close js-close-modal js-close-button">
        <use xlink:href="#popup-close"></use>
      </svg>
    </div>
    <?php else: ?>

  <div class="modal modal_content_how-it-work js-how-it-works">
    <h4 class="modal__title">
        <?= __('How it works'); ?>
    </h4>
    <div class="modal__body">
      <div class="how-it-work">
        <ul class="how-it-work__list">
          <li class="how-it-work__item">
            <div class="story story_content_how-it-work">
              <div class="story__thumbnail">
                <svg class="story__image story__image_step_1">
                  <use xlink:href="#instr6"></use>
                </svg>
              </div>

              <p class="story__text">
                  <?= __('Take any action from the proposed. More actions – more discount is!'); ?>
              </p>
            </div>
          </li>

          <li class="how-it-work__item how-it-work__item_content_arrow" aria-hidden="true">
            <svg class="how-it-work__arrow">
              <use xlink:href="#arrow"></use>
            </svg>
          </li>

          <li class="how-it-work__item">
            <div class="story story_content_how-it-work">
              <div class="story__thumbnail">
                <svg class="story__image story__image_step_2">
                  <use xlink:href="#instr7"></use>
                </svg>
              </div>

              <p class="story__text">
                  <?= __('Choose the discount size and get a promo-code.'); ?>
              </p>
            </div>
          </li>

          <li class="how-it-work__item how-it-work__item_content_arrow" aria-hidden="true">
            <svg class="how-it-work__arrow">
              <use xlink:href="#arrow"></use>
            </svg>
          </li>

          <li class="how-it-work__item">
            <div class="story story_content_how-it-work">
              <div class="story__thumbnail">
                <svg class="story__image story__image_step_3">
                  <use xlink:href="#instr8"></use>
                </svg>
              </div>

              <p class="story__text">
                  <?= __('Apply the promo code on the ticket purchase page.'); ?>
              </p>
            </div>
          </li>
        </ul>

        <div class="how-it-work__action">
          <button type="button" class="button button_viewtype_primary button_type_block c-primary-button js-close-modal js-understand-button">
            <span class="button__text">
              <?= __("Got it. Let's begin!"); ?>
            </span>
          </button>
        </div>
      </div>
    </div>
    <svg class="modal__close js-close-modal js-close-button">
      <use xlink:href="#popup-close"></use>
    </svg>
  </div>

    <?php endif; ?>
  <?php
                  } ?>



  <div class="modal modal_content_referrer-withdraw js-referrer-withdraw-modal" data-before-open-callback="REFERRER_MODAL_BEFORE_OPEN" data-close-callback="REFERRER_MODAL_CLOSE">
    <h4 class="modal__title">
      <?= __('Your withdrawal request has been successfully created.'); ?>
    </h4>
    <div class="modal__body">
      <?= __('%date% at %time% you requested a withdrawal of funds to the card %card% in the amount of %amount%.', ['date' => '<span class="js-transaction-open-date"></span>', 'time' => '<span class="js-transaction-open-time"></span>', 'card' => '<b class="js-transaction-card"></b>', 'amount' => '<b class="js-transaction-amount"></b>']); ?><br>
      <?= __('Bank commission is'); ?> <b class="js-transaction-fee"></b>.<br>
      <?= __('You will receive'); ?> <b class="js-transaction-total"></b> <span class="js-transaction-close-date"></span>.<br><br>
      <?= __('See the transaction status in&nbsp;the &laquo;Transaction List&raquo; table.'); ?>
    </div>
    <svg class="modal__close js-close-modal">
      <use xlink:href="#popup-close"></use>
    </svg>
  </div>

  <div class="modal modal_content_resend-code js-resend-code-modal">
    <h4 class="modal__title">
    </h4>
    <div class="modal__body">
      <p class="modal__text">
        <?php if ($partnerPage->partner->isAuthMethodPhone()) :?>
        <?= __("SMS with new confirmation code was sent to <strong class='js-confirm-phone'></strong>."); ?>
        <?php else:?>
        <?= __("New confirmation code was sent to <strong class='js-confirm-email'></strong>."); ?>
        <?php endif; ?>
      </p>
    </div>
    <div class="modal__footer">
      <button class="button button_viewtype_modal c-primary-button js-close-modal"><?= __('Login'); ?></button>
    </div>
  </div>

  <div class="modal modal_content_instagram-post js-instagram-post-modal">
    <h4 class="modal__title">
      <?= \HCustomizations::instagramActionSendUsPostMessage($partnerPage->viewData->partner); ?>
    </h4>
    <div class="modal__body">
      <check-post :has-url="1" type="instagram" inline-template>
        <form class="form form_content_instagram-post" @submit.prevent="onBeforeFormSubmit">
          <fieldset class="form-fields-group">
            <ul class="form-fields-list">
              <li class="form-fields-list__item">
                <dl class="field-composition">
                  <dt class="field-composition__title is-show">
                    <label class="form-field-label form-field-label_regular" for="post_url">
                      <?= __('Add link to post'); ?>
                    </label>
                  </dt>
                  <dd class="field-composition__content">
                    <div class="form-field-box">
                      <input type="text" class="form-field form-field_type_text is-small" v-model="url">
                    </div>
                  </dd>
                </dl>
              </li>

              <li class="form-fields-list__item">
                <vue-dropzone
                  ref="myVueDropzone"
                  id="dropzone"
                  @vdropzone-file-added="vfileAdded"
                  @vdropzone-error="verror"
                  :options="dropzoneOptions"
                >
                </vue-dropzone>
                <div id="tpl" style="display: none">
                  <div class="load-image">
                    <img data-dz-thumbnail class="load-image__icon" />
                    <span class="dz-filename"><span data-dz-name></span></span>
                    <div class="dz-error-message"><span data-dz-errormessage></span></div>
                  </div>
                </div>
              </li>

              <li class="form-fields-list__item">
                <div class="form-remark form-remark_viewtype_left">
                  <?= \HCustomizations::instagramActionDescriptionMessage($partnerPage->viewData->partner); ?>
                </div>
              </li>

              <li class="form-fields-list__item form-fields-list__item_content_submit">
                <button type="submit" class="button button_viewtype_mw button_viewtype_primary c-primary-button" v-if="!isSend" :class="{ 'is-load': isLoading }">
                  <span class="button__text">
                    <?= __('Submit for review'); ?>
                  </span>
                </button>
                <div class="progress" v-else>
                  <div class="progress__content" :style="progressStyle">
                    <span class="progress__text">
                      <?= __('Cheсk...'); ?> @{{ percent }}%
                    </span>
                  </div>
                  <div class="progress__timer">
                  <?= __('Time left'); ?> @{{ timeLeft | integer }} <?= __('s.'); ?>
                  </div>
                </div>
              </li>
            </ul>
          </fieldset>
        </form>
      </check-post>
    </div>
    <svg class="modal__close js-close-modal">
      <use xlink:href="#popup-close"></use>
    </svg>
  </div>

  <div class="modal modal_content_instagram-post js-subscribe-telegram-modal">
    <h4 class="modal__title">
      <?= __('Send us the screenshot of your subscription'); ?>
    </h4>
    <div class="modal__body">
      <check-post type="telegram" inline-template>
        <form class="form form_content_instagram-post" @submit.prevent="onBeforeFormSubmit">
          <fieldset class="form-fields-group">
            <ul class="form-fields-list">

              <li class="form-fields-list__item">
                <vue-dropzone
                  ref="myVueDropzone"
                  id="dropzone"
                  @vdropzone-file-added="vfileAdded"
                  @vdropzone-error="verror"
                  :options="dropzoneOptions"
                >
                </vue-dropzone>
                <div id="tpl" style="display: none">
                  <div class="load-image">
                    <img data-dz-thumbnail class="load-image__icon" />
                    <span class="dz-filename"><span data-dz-name></span></span>
                    <div class="dz-error-message"><span data-dz-errormessage></span></div>
                  </div>
                </div>
              </li>

              <li class="form-fields-list__item">
                <div class="form-remark form-remark_viewtype_left">
                  <?= __('Artificial Intelligence will check the content of the image and give you a reward.'); ?>
                </div>
              </li>

              <li class="form-fields-list__item form-fields-list__item_content_submit">
                <button type="submit" class="button button_viewtype_mw button_viewtype_primary c-primary-button" v-if="!isSend" :class="{ 'is-load': isLoading }">
                  <span class="button__text">
                    <?= __('Submit for review'); ?>
                  </span>
                </button>
                <div class="progress" v-else>
                  <div class="progress__content" :style="progressStyle">
                    <span class="progress__text">
                      <?= __('Cheсk...'); ?> @{{ percent }}%
                    </span>
                  </div>
                  <div class="progress__timer">
                  <?= __('Time left'); ?> @{{ timeLeft | integer }} <?= __('s.'); ?>
                  </div>
                </div>
              </li>
            </ul>
          </fieldset>
        </form>
      </check-post>
    </div>
    <svg class="modal__close js-close-modal">
      <use xlink:href="#popup-close"></use>
    </svg>
  </div>

  <div class="modal modal_content_instagram-post js-custom-social-action-modal">
    <h4 class="modal__title">
      <?= __('Send us the post'); ?>
    </h4>
    <div class="modal__body">
      <check-post :has-url="<?= json_encode($partnerPage->viewData->customSocialActionHasUrl); ?>" type="custom" inline-template>
        <form class="form form_content_instagram-post" @submit.prevent="onBeforeFormSubmit">
          <fieldset class="form-fields-group">
            <ul class="form-fields-list">
              @if ($partnerPage->viewData->customSocialActionHasUrl)
                <li class="form-fields-list__item">
                  <dl class="field-composition">
                    <dt class="field-composition__title is-show">
                      <label class="form-field-label form-field-label_regular" for="post_url">
                        <?= __('Add link to post'); ?>
                      </label>
                    </dt>
                    <dd class="field-composition__content">
                      <div class="form-field-box">
                        <input type="text" class="form-field form-field_type_text is-small" v-model="url">
                      </div>
                    </dd>
                  </dl>
                </li>
              @endif

              @if ($partnerPage->viewData->customSocialActionHasImage)
                <li class="form-fields-list__item">
                  <vue-dropzone
                          ref="myVueDropzone"
                          id="dropzone"
                          @vdropzone-file-added="vfileAdded"
                          @vdropzone-error="verror"
                          :options="dropzoneOptions"
                  >
                  </vue-dropzone>
                  <div id="tpl" style="display: none">
                    <div class="load-image">
                      <img data-dz-thumbnail class="load-image__icon" />
                      <span class="dz-filename"><span data-dz-name></span></span>
                      <div class="dz-error-message"><span data-dz-errormessage></span></div>
                    </div>
                  </div>
                </li>
              @endif

              <li class="form-fields-list__item">
                <div class="form-remark form-remark_viewtype_left">
                  <?= __('Artificial Intelligence will check the content of the image and give you a reward.'); ?>
                </div>
              </li>

              <li class="form-fields-list__item form-fields-list__item_content_submit">
                <button type="submit" class="button button_viewtype_mw button_viewtype_primary c-primary-button" v-if="!isSend" :class="{ 'is-load': isLoading }">
                    <span class="button__text">
                      <?= __('Submit for review'); ?>
                    </span>
                </button>
                <div class="progress" v-else>
                  <div class="progress__content" :style="progressStyle">
                      <span class="progress__text">
                        <?= __('Cheсk...'); ?> @{{ percent }}%
                      </span>
                  </div>
                  <div class="progress__timer">
                    <?= __('Time left'); ?> @{{ timeLeft | integer }} <?= __('s.'); ?>
                  </div>
                </div>
              </li>
            </ul>
          </fieldset>
        </form>
      </check-post>
    </div>
    <svg class="modal__close js-close-modal">
      <use xlink:href="#popup-close"></use>
    </svg>
  </div>
</div>
