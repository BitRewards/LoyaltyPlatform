<?php
/**
 * @var App\Models\Transaction $transaction
 */
?>
<div class="modal modal_content_auth js-transaction-{{ $transaction->id }}">
  <h4 class="modal__title">
    <?= __("Thank you!") ?>
  </h4>
  <div class="modal__body">
    <p class="modal__text">
      <?= __("Your discount") ?>: <b><?= $transaction->reward ? HReward::getValueStr($transaction->reward) : "—" ?></b>
    </p>
  </div>
  <div class="modal__footer">
    <a class="button button_viewtype_modal c-primary-button js-close-modal" href="<?= $transaction->data[\App\Models\Transaction::DATA_URL] ?>" target="_blank"><?= __("Use your discount") ?></a>
  </div>
  <svg class="modal__close js-close-modal">
    <use xlink:href="#popup-close"></use>
  </svg>
</div>
