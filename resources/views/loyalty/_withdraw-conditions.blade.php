<div class="box box_content_conditions">
  <h5 class="box__title">
    <?= __('Terms of payment'); ?>
  </h5>
  <div class="box__text pure-text">
    <ul>
      <?php if ($action) {
    ?>
        <li>
          <?= __('Minimum withdrawal amount:'); ?> <button type="button" class="field-remark__button" @click="amount = <?=($partnerSettings->fiatMinWithdrawAmount ?? 0); ?>"><?=$partnerSettings->fiatMinWithdraw; ?></button>
        </li>
        <li>
          <?= __('Maximum withdrawal amount:'); ?> <button type="button" class="field-remark__button" @click="amount = <?=($partnerSettings->fiatMaxWithdrawAmount ?? 0); ?>"><?=$partnerSettings->fiatMaxWithdraw; ?></button>
        </li>
      <?php
} else {
        ?>
        <li><?= __('Minimum withdrawal amount:'); ?> <strong><?=$partnerSettings->fiatMinWithdraw; ?></strong></li>
        <li><?= __('Maximum withdrawal amount:'); ?> <strong><?=$partnerSettings->fiatMaxWithdraw; ?></strong></li>
      <?php
    } ?>
      <li><?= __('Bank withdrawal fee:'); ?> <strong><?=$partnerSettings->fiatWithdrawFee; ?></strong></li>
      <li><?= __('Payment term: <strong>up to 7 days</strong>'); ?></li>
    </ul>
    <a class="link link_viewtype_standard i i_content_bold c-primary-color" href="<?= \HCustomizations::termsOfServiceLink($partnerPage->viewData->partner); ?>" target="_blank">
      <span class="i__text"><?= __('Terms of service'); ?></span>
    </a>
  </div>
</div>
