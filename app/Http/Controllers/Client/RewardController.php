<?php

namespace App\Http\Controllers\Client;

use App\DTO\PartnerPage\TransactionDataFactory;
use App\DTO\PartnerPageDataFactory;
use App\DTO\StoreEventData;
use App\Http\Controllers\Controller;
use App\Http\Requests\BitrewardsDepositRequest;
use App\Http\Requests\BitrewardsPayoutRequest;
use App\Http\Requests\BitrewardsUpdateWalletRequest;
use App\Http\Requests\FiatWithdrawRequest;
use App\Models\Partner;
use App\Models\Reward;
use App\Models\StoreEvent;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Fiat\FiatService;
use App\Services\PartnerService;
use App\Services\ReferralStatisticService;
use App\Services\StoreEventService;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RewardController extends Controller
{
    use AuthorizesRequests;

    /**
     * @var FiatService
     */
    private $fiatService;

    /**
     * @var PartnerPageDataFactory
     */
    private $partnerPageFactory;

    /**
     * @var TransactionDataFactory
     */
    private $transactionDataFactory;

    /**
     * @var \HAmount
     */
    private $amountHelper;

    /**
     * @var PartnerService
     */
    private $partnerService;

    public function __construct(
        FiatService $fiatService,
        PartnerPageDataFactory $pageFactory,
        TransactionDataFactory $transactionFactory,
        \HAmount $amountHelper,
        PartnerService $partnerService
    ) {
        $this->fiatService = $fiatService;
        $this->amountHelper = $amountHelper;
        $this->partnerPageFactory = $pageFactory;
        $this->transactionDataFactory = $transactionFactory;
        $this->partnerService = $partnerService;
    }

    public function acquire(Partner $partner, Reward $reward)
    {
        $user = \Auth::user();
        $transaction = $reward->getRewardProcessor()->acquire($user, [], $user);

        return jsonResponse([
            'url' => route(
                'client.reward.usageModal', [
                    'partner' => $reward->partner->key,
                    'transaction' => $transaction->id,
                ]
            ),
        ]);
    }

    public function usageModal(Partner $partner, Transaction $transaction)
    {
        if ($transaction->data[Transaction::DATA_NOT_USABLE_BY_CLIENT] ?? false) {
            throw new NotFoundHttpException();
        }

        return view('loyalty/_transaction-usage-modal', compact('transaction'));
    }

    public function bitrewardsPaidOutModal(Partner $partner, Transaction $transaction)
    {
        return view('loyalty/_bitrewards-paid-out-modal', compact('transaction'));
    }

    protected function getPartnerCurrencyBitExchangeRate(Partner $partner)
    {
        $isoCurrency = $this->amountHelper::sISO4217($partner->currency);

        return $this->fiatService->getExchangeRate('BIT', $isoCurrency);
    }

    public function bitrewardsWithdrawTransactions(Partner $partner, $transactions)
    {
        return view('loyalty/_bitrewards-withdraw-transactions', [
            'partner' => $partner,
            'transactions' => $transactions,
            'partnerCurrency' => $partner->currency,
            'partnerCurrencyBitExchangeRate' => $this->getPartnerCurrencyBitExchangeRate($partner),
            'partnerPage' => $this->partnerPageFactory->factory($partner, \Auth::user()),
        ]);
    }

    public function bitrewardsDepositTransactions(Partner $partner, $transactions)
    {
        return view('loyalty/_bitrewards-deposit-transactions', [
            'partner' => $partner,
            'transactions' => $transactions,
            'partnerCurrency' => $partner->currency,
            'partnerCurrencyBitExchangeRate' => $this->getPartnerCurrencyBitExchangeRate($partner),
        ]);
    }

    /**
     * @param BitrewardsPayoutRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \App\Exceptions\RewardAcquiringException
     */
    public function bitrewardsPayout(BitrewardsPayoutRequest $request)
    {
        $partner = $request->partner;
        $bitrewardsPayout = $partner->getRewardByType(Reward::TYPE_BITREWARDS_PAYOUT);

        if (!$bitrewardsPayout || (!\App::isLocal() && !$partner->getSetting(Partner::SETTINGS_BITREWARDS_ENABLED))) {
            abort(404);
        }

        $withdrawPartner = Partner::model()->whereAttributes(['eth_address' => $request->get('withdraw_eth')])->first();

        if ($withdrawPartner && $withdrawPartner->id === $partner->id) {
            return jsonError(['withdraw_eth' => __("The withdraw address cannot be equal to the partner's wallet address")]);
        }

        $amount = (int) $request->get('token_amount');
        $fee = $partner->getBitWithdrawFeeForAmount($amount);

        if ($amount && $amount + $fee < $partner->getBitWithdrawMinAmount()) {
            return jsonError(['token_amount' => __('Amount below acceptable')]);
        }

        $transactionData = [
            Transaction::DATA_ETHEREUM_ADDRESS => $request->get('withdraw_eth'),
            Transaction::DATA_POINTS_TO_SPEND => $amount + $fee,
            Transaction::DATA_BITREWARDS_PAYOUT_AMOUNT => $amount,
            Transaction::DATA_BITREWARDS_WITHDRAW_FEE => $fee,
            Transaction::DATA_BITREWARDS_WITHDRAW_FEE_TYPE => $partner->getBitWithdrawFeeType(),
            Transaction::DATA_BITREWARDS_WITHDRAW_FEE_VALUE => $partner->getBitWithdrawFee(),
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

        /** @var User $user */
        $user = \Auth::user();
        $transaction = $bitrewardsPayout->getRewardProcessor()->acquire($user, $transactionData, $user);

        $withdrawTransactions = $this->transactionDataFactory->factoryBitrewardsPayoutTransactions($partner, \Auth::user());
        $depositTransactions = $this->transactionDataFactory->factoryDepositTransactions($partner, \Auth::user());

        return jsonResponse([
            'url' => route(
                'client.reward.bitrewardsPaidOutModal', [
                    'partner' => $bitrewardsPayout->partner->key,
                    'transaction' => $transaction->id,
                ]
            ),
            'tab' => 'history',
            'magic_number' => $magicNumber,
            'withdraw_transactions' => $this->bitrewardsWithdrawTransactions($partner, $withdrawTransactions)->render(),
            'deposit_transactions' => $this->bitrewardsDepositTransactions($partner, $depositTransactions)->render(),
            'balance' => $user ? $user->balance : 0,
        ]);
    }

    public function confirmDeposit(BitrewardsDepositRequest $request)
    {
        $partner = $request->partner;
        $bitrewardsPayout = $partner->getRewardByType(Reward::TYPE_BITREWARDS_PAYOUT);

        if (!$bitrewardsPayout || (!\App::isLocal() && !$partner->getSetting(Partner::SETTINGS_BITREWARDS_ENABLED))) {
            abort(404);
        }

        $withdrawTransaction = Transaction::model()
            ->whereNotNull('reward_id')
            ->where('data->'.Transaction::DATA_MAGIC_NUMBER, '=', $request->deposit_magic)
            ->first();

        if (!$withdrawTransaction || Reward::TYPE_BITREWARDS_PAYOUT !== $withdrawTransaction->reward->type) {
            return jsonError(['deposit_magic' => __('Magic number not found')]);
        }

        if (!empty($withdrawTransaction->data[Transaction::DATA_CHILD_REFILL_BIT_TRANSACTION_ID])) {
            return jsonError(['deposit_magic' => __('Magic number is expired')]);
        }

        if (Transaction::STATUS_CONFIRMED !== $withdrawTransaction->status) {
            return jsonError(['deposit_magic' => __('Please wait until withdrawal transaction gets confirmed')]);
        }

        $validMerchant = $partner->eth_address &&
            $partner->eth_address === $withdrawTransaction->data[Transaction::DATA_ETHEREUM_ADDRESS];

        if (!$validMerchant) {
            return jsonError(['deposit_magic' => __('Magic number not found')]);
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

        return jsonResponse([
            'transactions' => $this->bitrewardsDepositTransactions($partner, $bitrewardsTransactions['deposit'] ?? [])->render(),
            'balance' => $user ? $user->balance : 0,
        ]);
    }

    public function updateWalletAddress(BitrewardsUpdateWalletRequest $request)
    {
        $partner = $request->partner;
        $user = \Auth::user();

        // check that no partner or user with such wallet exists
        if (
            Partner::model()->where('eth_address', '=', $request->ethereum_wallet)->count() ||
            User::model()
                ->where('bit_tokens_sender_address', '=', $request->ethereum_wallet)
                ->where('id', '<>', $user->id)->count()
        ) {
            return jsonError(['ethereum_wallet' => __('Wallet is already in use by another user')]);
        }

        if ($user->bit_tokens_sender_address != $request->ethereum_wallet) {
            \DB::beginTransaction();

            try {
                $user->bit_tokens_sender_address = $request->ethereum_wallet;

                if (!$user->save()) {
                    throw new \InvalidArgumentException();
                }

                \DB::table('bit_tokens_sender_address_history')->insert(
                    [
                        'user_id' => $user->id,
                        'address' => $user->bit_tokens_sender_address,
                        'created_at' => Carbon::now(),
                    ]
                );
                \DB::commit();
            } catch (\Throwable $e) {
                \DB::rollBack();

                return jsonError();
            }

            return jsonResponse();
        } else {
            return jsonResponse();
        }
    }

    public function updateEthWalletAddress(BitrewardsUpdateWalletRequest $request)
    {
        $partner = $request->partner;
        $user = \Auth::user();

        // check that no partner or user with such wallet exists
        if (
            Partner::model()->where('eth_address', '=', $request->ethereum_wallet)->count() ||
            User::model()
                ->where('eth_sender_address', '=', $request->ethereum_wallet)
                ->where('id', '<>', $user->id)->count()
        ) {
            return jsonError(['ethereum_wallet' => __('Wallet is already in use by another user')]);
        }

        $user->eth_sender_address = $request->ethereum_wallet;

        if ($user->save()) {
            return jsonResponse();
        } else {
            return jsonError();
        }
    }

    public function fiatWithdraw(Partner $partner, FiatWithdrawRequest $request)
    {
        $fiatWithdrawReward = $request->partner->getRewardByType(Reward::TYPE_FIAT_WITHDRAW);

        if (!$fiatWithdrawReward) {
            abort(404);
        }

        /** @var User $user */
        $user = \Auth::user();
        $referrerBalance = app(ReferralStatisticService::class)->getReferrerBalance($user);

        if ($referrerBalance->availableForWithdrawAmount < $request->withdrawAmount) {
            return jsonError(['withdrawAmount' => __('Not enough free funds for withdraw')]);
        }

        $feeAmount = $this
            ->partnerService
            ->calculateFiatWithdrawFeeAmount($partner, $request->withdrawAmount);

        $transactionData = [
            Transaction::DATA_FIAT_WITHDRAW_CARD_NUMBER => $request->cardNumber,
            Transaction::DATA_FIAT_WITHDRAW_FIRST_NAME => $request->firstName,
            Transaction::DATA_FIAT_WITHDRAW_LAST_NAME => $request->secondName,
            Transaction::DATA_FIAT_WITHDRAW_FEE => $feeAmount,
            Transaction::DATA_POINTS_TO_SPEND => $this->amountHelper::fiatToPoints($request->withdrawAmount, $partner),
            Transaction::DATA_FIAT_WITHDRAW_FEE_TYPE => $partner->getFiatWithdrawFeeType(),
            Transaction::DATA_FIAT_WITHDRAW_FEE_VALUE => $partner->getFiatWithdrawFee(),
            Transaction::DATA_FIAT_WITHDRAW_AMOUNT => $request->withdrawAmount - $feeAmount,
            Transaction::DATA_FIAT_WITHDRAW_EXTERNAL_OPERATION_ID => '',
        ];

        $transaction = $fiatWithdrawReward->getRewardProcessor()->acquire($user, $transactionData, $user);

        return jsonResponse();
    }
}
