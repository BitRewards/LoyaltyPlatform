<?php
/**
 * @var App\Models\Transaction $transaction
 */
?>
<div class="modal modal_content_auth modal_content_bitrewards-paid-out js-transaction-{{ $transaction->id }}">
  <h4 class="modal__title">
    <?= __("Thank you!") ?>
  </h4>
  <div class="modal__body">
    <p class="modal__text">
      <?= __("%amount% BIT will be transferred to your Ethereum wallet <b>%address%</b> soon after BitRewards ICO finishes", [
          'amount' => $transaction->data[App\Models\Transaction::DATA_BITREWARDS_PAYOUT_AMOUNT],
          'address' => $transaction->data[App\Models\Transaction::DATA_ETHEREUM_ADDRESS]
        ]) ?>.
    </p>
  </div>
  <div class="modal__footer">
    <button type="button" class="button button_viewtype_modal c-primary-button js-close-modal">
      <?= __("Got it") ?>
    </button>
  </div>
  <svg class="modal__close js-close-modal">
    <use xlink:href="#popup-close"></use>
  </svg>
</div>
