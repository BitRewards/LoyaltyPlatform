<table class="table table_content_transactions">
    <thead class="table__head">
    <tr class="table__row is-canceled">
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
            <?php /* @var \App\DTO\PartnerPage\TransactionData $transaction */?>
            <tr class="table__row">
                <td class="table__cell" data-label="<?=__("ID")?>">
                    <div class="table__cell-text">
                        <?= $transaction->id ?>
                    </div>
                </td>

                <td class="table__cell" data-label="<?=__("Date/time")?>">
                    <div class="table__cell-text">
                        <span class="transaction__date">
                          <?=$transaction->viewData->createdDateFull?>
                        </span>
                        <span class="transaction__meta">
                          <?=$transaction->viewData->createTime?>
                        </span>
                    </div>
                </td>

                <td class="table__cell table__cell_content_sender" data-label="<?=__("Sender")?>">
                    <div class="table__cell-text">
                        <span class="transaction__desc">
                          <?=$transaction->viewData->bitrewardsSenderActor ?>
                        </span>
                        <span class="transaction__meta">
                          <?=$transaction->viewData->bitrewardsSenderAddress ?>
                        </span>
                    </div>
                </td>

                <td class="table__cell" data-label="<?=__("Amount")?>">
                    <div class="table__cell-text">
                        <span class="transaction__desc">
                          + <?= $transaction->balanceChangeAmount ?> BIT
                        </span>
                        <span class="transaction__meta">
                        = <?=$transaction->viewData->balanceChangeInPartnerCurrency?>
                        </span>
                        <?php if ($transaction->isBitrewardsExchangeEthToBit): ?>
                        <span class="transaction__meta">
                        = <?= $transaction->treasuryEthAmount?> ETH
                        </span>
                        <?php endif; ?>
                    </div>
                </td>

                <td class="table__cell" data-label="<?=__("Status")?>">
                    <div class="table__cell-text">
                        <span class="transaction__status transaction__status_viewtype_wait">
                          <?=$transaction->status ?>
                        </span>
                    </div>
                </td>
            </tr>
        @endforeach
        @endif
    </tbody>
</table>
