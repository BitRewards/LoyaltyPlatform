<li class="form-fields-list__item">
  <label for="<?= $id; ?>" class="checkbox c-primary-checkbox">
    <span class="checkbox__in">
        <input type="checkbox" id="<?= $id ? $id : 'privacy_policy'; ?>" name="<?= $id; ?>" class="checkbox__input" checked>
        <span class="checkbox__pseudo"></span>
    </span>
    <span class="checkbox__text"><?= __('I agree to the'); ?>
      <a class="link link_viewtype_standard c-primary-color" href="#" target="_blank"><?= __('Privacy Policy'); ?></a>
      <?= __('and'); ?>&nbsp;<a class="link link_viewtype_standard c-primary-color" href="<?= \HCustomizations::termsOfServiceLink($partnerPage->viewData->partner); ?>" target="_blank"><?= __('Terms of Service'); ?></a>
    </span>
  </label>

</li>
