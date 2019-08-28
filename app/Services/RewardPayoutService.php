<?php
/**
 * RewardPayoutService.php
 * Creator: lehadnk
 * Date: 20/07/2018.
 */

namespace App\Services;

use App\DTO\PartnerPage\TransactionDataFactory;
use App\DTO\StoreEventData;
use App\Exceptions\ValidationConstraintException;
use App\Models\Partner;
use App\Models\Reward;
use App\Models\StoreEvent;
use App\Models\Transaction;

class RewardPayoutService
{
    /**
     * @var TransactionDataFactory
     */
    private $transactionDataFactory;

    public function __construct(TransactionDataFactory $transactionDataFactory)
    {
        $this->transactionDataFactory = $transactionDataFactory;
    }

    public function bitrewardsPayout(Partner $partner, string $ethAddress, int $tokenAmount): Transaction
    {
        $bitrewardsPayout = $partner->getRewardByType(Reward::TYPE_BITREWARDS_PAYOUT);

        if (!$bitrewardsPayout || !$partner->getSetting(Partner::SETTINGS_BITREWARDS_ENABLED)) {
            throw new \DomainException();
        }

        $withdrawPartner = Partner::model()->whereAttributes(['eth_address' => $ethAddress])->first();

        if ($withdrawPartner && $withdrawPartner->id === $partner->id) {
            throw new ValidationConstraintException(
                'withdraw_eth',
                __("The withdraw address cannot be equal to the partner's wallet address")
            );
        }

        $fee = $partner->getBitWithdrawFeeForAmount($tokenAmount);

        if ($tokenAmount + $fee < $partner->getBitWithdrawMinAmount()) {
            throw new ValidationConstraintException(
                'token_amount',
                __('Amount below acceptable')
            );
        }

        $transactionData = [
            Transaction::DATA_ETHEREUM_ADDRESS => $ethAddress,
            Transaction::DATA_POINTS_TO_SPEND => $tokenAmount + $fee,
            Transaction::DATA_BITREWARDS_PAYOUT_AMOUNT => $tokenAmount,
            Transaction::DATA_BITREWARDS_WITHDRAW_FEE => $fee,
        ];

        $magicNumber = null;

        if ($withdrawPartner) {
            $index = 0;

            while (++$index < 10 && $magicNumber = sprintf('%08d%08d', random_int(0, 99999999), random_int(0, 99999999))) {
                if (!Transaction::model()->whereAttributes(['data->'.Transaction::DATA_MAGIC_NUMBER => $magicNumber])->get()) {
                    // no such magic number in db

                    break;
                }
            }
            $transactionData[Transaction::DATA_MAGIC_NUMBER] = $magicNumber;
        }

        $user = \Auth::user();
        $transaction = $bitrewardsPayout->getRewardProcessor()->acquire($user, $transactionData, $user);

        $withdrawTransactions = $this->transactionDataFactory->factoryBitrewardsPayoutTransactions($partner, \Auth::user());
        $depositTransactions = $this->transactionDataFactory->factoryDepositTransactions($partner, \Auth::user());

        return [
            'url' => route(
                'client.reward.bitrewardsPaidOutModal', [
                    'partner' => $bitrewardsPayout->partner->key,
                    'transaction' => $transaction->id,
                ]
            ),
            'tab' => 'history',
            'magic_number' => $magicNumber,
            'withdraw_transactions' => $withdrawTransactions,
            'deposit_transactions' => $depositTransactions,
            'balance' => $user ? $user->balance : 0,
        ];
    }

    public function confirmDeposit(Partner $partner, $depositMagic)
    {
        $bitrewardsPayout = $partner->getRewardByType(Reward::TYPE_BITREWARDS_PAYOUT);

        if (!$bitrewardsPayout || !$partner->getSetting(Partner::SETTINGS_BITREWARDS_ENABLED)) {
            abort(404);
        }

        $withdrawTransaction = Transaction::model()
            ->whereNotNull('reward_id')
            ->where('data->'.Transaction::DATA_MAGIC_NUMBER, '=', $depositMagic)
            ->first();

        if (!$withdrawTransaction || Reward::TYPE_BITREWARDS_PAYOUT !== $withdrawTransaction->reward->type) {
            throw new ValidationConstraintException('magic_number', 'Magic number not found', null);
        }

        if (!empty($withdrawTransaction->data[Transaction::DATA_CHILD_REFILL_BIT_TRANSACTION_ID])) {
            throw new ValidationConstraintException('magic_number', 'Magic number is expired', null);
        }

        if (Transaction::STATUS_CONFIRMED !== $withdrawTransaction->status) {
            throw new ValidationConstraintException('deposit_magic', 'Please wait until withdrawal transaction gets confirmed', null);
        }

        $validMerchant = $partner->eth_address &&
            $partner->eth_address === $withdrawTransaction->data[Transaction::DATA_ETHEREUM_ADDRESS];

        if (!$validMerchant) {
            throw new ValidationConstraintException('magic_number', 'Magic number not found', null);
        }

        $user = \Auth::user();

        $eventData = new StoreEventData(StoreEvent::ACTION_REFILL_BIT);

        $transactionData = $withdrawTransaction->data->toArray();

        $eventData->data = [
            StoreEventData::DATA_KEY_TREASURY_DATA => $transactionData[Transaction::DATA_TREASURY_DATA] ?? null,
            StoreEventData::DATA_KEY_PARENT_TRANSACTION_ID => $withdrawTransaction->id,
            StoreEventData::DATA_KEY_TREASURY_SENDER_ADDRESS => $withdrawTransaction->partner->eth_address,
            'amount' => $transactionData[Transaction::DATA_BITREWARDS_PAYOUT_AMOUNT] ?? null,
        ];
        $eventData->data['userCrmKey'] = $user->key;
        app(StoreEventService::class)->saveAndHandle($partner, $eventData);

        $user = app(UserService::class)->recalculateBalance($user);

        $bitrewardsTransactions = $user ? $user->getBitrewardsTransactions() : [];

        return [
            'transactions' => $bitrewardsTransactions['deposit'] ?? [],
            'balance' => $user ? $user->balance : 0,
        ];
    }
}
