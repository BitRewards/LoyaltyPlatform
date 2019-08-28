<?php
use App\Models\Transaction;
/**
 * @var \App\DTO\PartnerPageData $partnerPage
 * @var \App\DTO\PartnerPage\TransactionData[] $transactions
 * @var \App\DTO\PartnerPage\TransactionData $transaction
 */
?>
<table class="table table_content_transactions">
    <thead class="table__head">
    <tr class="table__row">
        <th class="table__heading">
            <?=__("ID")?>
        </th>
        <th class="table__heading">
            <?=__("Date/time")?>
        </th>
        <th class="table__heading">
            <?=__("Sender")?>
        </th>
        <th class="table__heading">
            <?=__("Amount")?>
        </th>
        <th class="table__heading">
            <?=__("Fee")?>
        </th>
        <th class="table__heading">
            <?=__("Status")?>
        </th>
        <th class="table__heading">
            &nbsp;
        </th>
    </tr>
    </thead>

    <tbody>
    @if (isset($transactions) && count($transactions) > 0)
        @foreach ($transactions as $transaction)
            <?php $transactionData = $transaction->data->toArray();?>
            <tr class="table__row">
                <td class="table__cell" data-label="<?=__("ID")?>">
                    <div class="table__cell-text">
                        <?= $transaction->id ?>
                    </div>
                </td>

                <td class="table__cell" data-label="<?=__("Date/time")?>">
                    <div class="table__cell-text">
                        <span class="transaction__date">
                          <?=$transaction->viewData->createDate?>
                        </span>
                        <span class="transaction__meta">
                          <?=$transaction->viewData->createTime?>
                        </span>
                    </div>
                </td>

                <td class="table__cell table__cell_content_sender" data-label="<?=__("Sender")?>">
                    <div class="table__cell-text">
                        <span class="transaction__desc">
                          <?=$partnerPage->partner->title?>
                        </span>
                        <span class="transaction__meta">
                          <?=$partnerPage->partner->ethAddress?>
                        </span>
                    </div>
                </td>

                <td class="table__cell" data-label="<?=__("Amount")?>">
                    <div class="table__cell-text">
                        <span class="transaction__desc">
                          <?=$transaction->payoutAmount()?> BIT
                        </span>
                        <span class="transaction__meta">
                        = <?=$transaction->viewData->payoutAmountInPartnerCurrency ?>
                        </span>
                    </div>
                </td>

                <td class="table__cell" data-label="<?=__("Fee")?>">
                    <div class="table__cell-text">
                        <span class="transaction__desc">
                          <?=$transaction->withdrawFee()?> BIT
                        </span>
                        <span class="transaction__meta">
                          <?= $transactionData[Transaction::DATA_BITREWARDS_WITHDRAW_FEE_VALUE] ?? '30'?> <?= ($transactionData[Transaction::DATA_BITREWARDS_WITHDRAW_FEE_TYPE] ?? 'percent') === 'percent' ? '%' : 'BIT' ;?>
                        </span>
                    </div>
                </td>

                <td class="table__cell" data-label="<?=__("Status")?>">
                    <div class="table__cell-text">
                        <span class="transaction__status transaction__status_viewtype_wait">
                          <?=$transaction->status ?>
                        </span>
                    </div>
                </td>

                <td class="table__cell">
                    <div class="table__cell-text">
                        @if (!empty($transaction->magicNumber()))
                        <div class="dropdown js-dropdown" data-id="">
                            <input type="hidden" class="js-transaction-id" value="<?= $transaction->id ?>" />
                            <input type="hidden" class="js-transaction-address" value="<?= $transaction->ethereumAddress()?>" />
                            <input type="hidden" class="js-transaction-amount" value="<?= $transaction->payoutAmount()?>" />
                            <input type="hidden" class="js-transaction-magic" value="<?= $transaction->magicNumber()?>" />
                            <div class="dropdown__in js-dropdown-opener">
                                <svg class="dropdown__icon">
                                    <use xlink:href="#hellip"></use>
                                </svg>
                            </div>
                            <div class="dropdown__content">
                                <div class="dropdown__item js-dropdown-number">
                                    <?= __("Get magic number") ?>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>