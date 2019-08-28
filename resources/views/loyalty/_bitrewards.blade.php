<?php
/**
 * @var \App\DTO\PartnerPageData $partnerPage
 */
?>

<?php if ($partnerPage->user) { ?>

<div class="tab-content tab-content_viewtype_centered is-hide js-tab-content js-updatable" data-id="bitrewards" data-block-id="bitrewards-payout-section">
  <div class="tab-content__in tab-content__in_content_balance">
    <div class="tab-content__header">
      <h2 class="tab-content__title tab-content__title_viewtype_centered tab-content__title_content_brw c-text i i_content_brand">
          <?= __("Your balance") ?>:<br>
          <span class="i__text c-primary-color">
            <b>
              <?= $partnerPage->user->getBalance() ?>
            </b>
          </span>
      </h2>
      <div class="tab-content__subtitle tab-content__subtitle_viewtype_small">
        <?= __("You may spend your BIT tokens on our rewards<br>or transfer them to your Ethereum wallet") ?>
      </div>
    </div>
    <div class="tab-content__body">
      <div class="ethereum-output">
        <div class="ethereum-output__action">
          <button type="button" class="button button_viewtype_primary button_type_block is-small c-primary-button js-show-modal" data-modal=".js-ethereum-output-modal">
            <span class="button__text">
              <?= __('Withdraw %s BIT', $partnerPage->partner->brwAmount) ?>
            </span>
          </button>
        </div>
        <div class="ethereum-output__footer">
          <ul class="exchange-rates">
            <li class="exchange-rates__item">
              1 BIT = $<?= $partnerPage->partner->brwToUsdRate ?>
              <a class="link link_content_external" href="https://coinmarketcap.com/"  target="_blank" title="<?= __("Go to exchange") ?>">
                <svg class="link__icon c-primary-fill" aria-hidden="true">
                  <use xlink:href="#link"></use>
                </svg>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<?php } else { ?>

<?php } ?>
