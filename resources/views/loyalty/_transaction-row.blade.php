<?php
/**
 * @var \App\DTO\PartnerPageData
 * @var \App\DTO\PartnerPage\TransactionData $transaction
 */
?>

@if($transaction->type === \App\Models\Transaction::TYPE_REWARD)
<li class="list__item history-list__item_viewtype_get-discount {{ $transaction->isDataNotUsableByClient() ? 'hotfix-reward-history-row' : 'js-transaction-show-usage-modal hotfix-reward-history-row' }}"
    data-hover-title="{{ __('Click here to use the discount') }}"
    data-usage-url="{{ $transaction->viewData->usageModalUrl }}">
    <div class="operation c-text">
        <div class="operation__thumbnail operation__thumbnail_viewtype_get-discount">
            <svg class="operation__icon operation__icon_content_get-discount c-primary-fill ">
                <use xlink:href="#discount"></use>
            </svg>
        </div>

        <div class="operation__description">
            <span class="operation__meta">{{ $transaction->created }}</span>
            <div class="operation__title js-title">{!! $transaction->title !!}</div>
        </div>

        <?php if (!$transaction->isGradedPercentRewardModeEnabled): ?>
            <div class="operation__price {{ $transaction->balanceChangeAmount < 0 ? 'operation__price_viewtype_spent' : '' }}">
                {!! $transaction->viewData->balanceChange !!}
            </div>
        <?php endif; ?>

        <div class="operation__action">
            <button type="button"
                    class="operation__status operation__status_viewtype_get-discount c-primary-button"
                    style="{{ $transaction->isDataNotUsableByClient() ? 'display: none;' : '' }}">
                {{ __('Redeem') }}
            </button>
        </div>
    </div>
</li>
@elseif($transaction->type === \App\Models\Transaction::TYPE_ACTION)
<li class="list__item"
    data-hover-title="{{ __('Click here to use the discount') }}"
    data-usage-url="{{ $transaction->viewData->usageModalUrl }}">
    <div class="operation c-text">
        <div class="operation__thumbnail c-primary-bg {{ $transaction->isExpired ? 'operation__thumbnail_viewtype_burned' : 'operation__thumbnail' }}">
            <ins class="operation__icon icon icon_content_{{ $transaction->viewData->iconClass }} "></ins>
        </div>

        <div class="operation__description">
            <span class="operation__meta">
                {{ $transaction->created }}
                <span  class="operation__meta operation__meta_viewtype_expired c-meta">{!! $transaction->outputBalanceExpiresAtExtraStr !!}</span>
            </span>

            <div class="operation__title js-title">{!! $transaction->title !!}</div>
        </div>
        <?php if (!$transaction->isGradedPercentRewardModeEnabled): ?>
            <div class="operation__price {{ $transaction->balanceChangeAmount < 0 ? 'operation__price_viewtype_spent' : '' }}">
                {!! $transaction->viewData->balanceChange !!}
            </div>
        <?php endif; ?>

        <div class="operation__status {{ $transaction->isExpired ? 'operation__status_viewtype_burned' : 'operation__status_viewtype_' . $transaction->viewData->iconStatusClass }} c-primary-color c-primary-status js-tooltip"
             data-tooltip-text="{{ $transaction->status }}">
            {{ $transaction->status }}
        </div>
    </div>
</li>
@elseif($transaction->type === \App\Models\Transaction::TYPE_EXPIRATION)
<li class="list__item">
    <div class="operation c-text">
        <div class="operation__thumbnail operation__thumbnail_transparent">
            <svg class="operation__icon icon icon_content_svg c-primary-fill">
                <use xlink:href="#burn"></use>
            </svg>
        </div>

        <div class="operation__description">
            <span class="operation__meta">{{ $transaction->created }}</span>
            <div class="operation__title js-title">{{ __('Burn points') }}</div>
        </div>
        <?php if (!$transaction->isGradedPercentRewardModeEnabled): ?>
            <div class="operation__price {{ $transaction->balanceChangeAmount < 0 ? 'operation__price_viewtype_spent' : '' }}">
                {!! $transaction->viewData->balanceChange !!}
            </div>
        <?php endif; ?>

        <div class="operation__status operation__status_viewtype_{{ $transaction->viewData->iconStatusClass }} c-primary-status js-tooltip" data-tooltip-text="{{ $transaction->status }}">
            {{ $transaction->status }}
        </div>
    </div>
</li>
@endif