<?php
/**
 * @var FrontendController
 * @var \App\DTO\PartnerPageData $partnerPage
 */
?>

<div class="tab-content is-hide js-tab-content" data-id="help">
  <div class="tab-content__in tab-content__in_content_faq">
    <div class="tab-content__header content-columns content-columns_content_faq-header">
      <div class="content-column content-column_layout_a i i_content_mobile-hide">
        <div class="content-column__in">
          <h2 class="tab-content__title tab-content__title_viewtype_small c-text">
            <?= __('FAQs'); ?>
          </h2>
        </div>
      </div>
      <div class="content-column content-column_layout_b">
        <div class="content-column__in">
          <button class="button button_viewtype_modal c-primary-button button_content_ask-question js-show-modal" data-modal=".js-ask-question-modal">
            <?= __('Ask a question'); ?>
          </button>
        </div>
      </div>
    </div>
    <div class="tab-content__body tab-content__body_viewtype_fixed">
      <div class="scroller">
        <div class="scroller__in">
          <ul class="questions js-faq-content">
            <li class="questions__item">
              @foreach ($partnerPage->helpItems as $helpItem)
              <dl class="faq c-faq c-text js-faq">
                <dt class="faq__question">
                  <?=$helpItem->question; ?>
                  <svg class="faq__icon c-faq-icon">
                    <use xlink:href="#question"></use>
                  </svg>
                </dt>
                <dd class="faq__answer" >
                  <?=nl2br($helpItem->answer); ?>
                </dd>
              </dl>
              @endforeach
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
