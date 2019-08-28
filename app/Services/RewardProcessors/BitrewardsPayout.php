<?php

namespace App\Services\RewardProcessors;

use App\Exceptions\RewardAcquiringException;
use App\Models\Transaction;
use App\Services\Treasury\IApiClient;
use App\Services\Treasury\TreasuryException;

class BitrewardsPayout extends Base
{
    protected function isExecutedOnAcquire(): bool
    {
        return true;
    }

    protected function isConfirmedOnExecute(): bool
    {
        return false;
    }

    /**
     * @param Transaction $transaction
     *
     * @return array
     *
     * @throws RewardAcquiringException
     */
    protected function executeRewardInternal(Transaction $transaction): array
    {
        $points = $transaction->getPointsSpent();
        $address = $transaction->data[Transaction::DATA_ETHEREUM_ADDRESS] ?? null;

        if (!$address) {
            throw new RewardAcquiringException('No ETH address found in transaction data!');
        }

        $v = \Validator::make(['address' => $address], [
            'address' => 'ethaddress',
        ]);

        if ($v->fails()) {
            throw new RewardAcquiringException('Invalid ETH address');
        }

        $brwAmount = $transaction->data[Transaction::DATA_BITREWARDS_PAYOUT_AMOUNT] ?? $points; // round($points * $exchangeRate, 8);
        $partner = $transaction->partner;

        try {
            $withdrawal = app(IApiClient::class,
                ['userId' => $partner->id, 'apiKey' => $partner->mainAdministrator->api_token])
                ->withdraw(
                    $address,
                    $brwAmount,
                    \HCurrency::CURRENCY_BIT,
                    $partner->withdraw_key,
                    route('api.treasury.transaction.callback', [
                        'transactionId' => $transaction->id,
                    ], true)
                );
        } catch (TreasuryException $e) {
            throw new RewardAcquiringException($e->getMessage(), [$transaction]);
        }

        $result = [
            Transaction::DATA_NOT_USABLE_BY_CLIENT => true,
            Transaction::DATA_TREASURY_RESPONSE => $withdrawal,
        ];

        return $result;
    }
}
