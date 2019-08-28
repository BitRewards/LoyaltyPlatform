<?php

namespace App\Fractal\Transformer\PartnerPage;

use App\DTO\PartnerPage\TransactionData;
use League\Fractal\TransformerAbstract;

class TransactionDataTransformer extends TransformerAbstract
{
    public function transform(TransactionData $transactionData): array
    {
        return [
            'id' => $transactionData->id,
            'title' => $transactionData->title,
            'rewardId' => $transactionData->rewardId,
            'actionId' => $transactionData->actionId,
            'status' => $transactionData->status,
            'data' => $transactionData->data,
            'isBitrewardPayout' => $transactionData->isBitrewardPayout,
            'payoutAmount' => $transactionData->payoutAmount,
            'balanceChangeAmount' => $transactionData->balanceChangeAmount,
            'withdrawFee' => $transactionData->withdrawFee,
            'createdAt' => $transactionData->createdAt,
            'viewData' => [
            ],
        ];
    }
}
