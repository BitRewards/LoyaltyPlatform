<?php

namespace App\Jobs;

use App\DTO\StoreEventData;
use App\DTO\TransactionData;
use App\Http\Requests\Api\TransactionCallbackRequest;
use App\Models\EthTransfer;
use App\Models\Partner;
use App\Models\StoreEvent;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Fiat\FiatService;
use App\Services\PartnerService;
use App\Services\StoreEventService;
use App\Services\TransactionService;
use App\Services\UserService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\Treasury\ApiClient as TreasuryClient;
use App\Services\Treasury\TreasuryException;

class HandleTreasuryCallback implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $type;
    protected $requestData;
    protected $callbackData;

    public function __construct($type, $requestData, $callbackData)
    {
        $this->type = $type;
        $this->requestData = $requestData;
        $this->callbackData = $callbackData;
    }

    protected function handleWithdrawalCallback()
    {
        $transactionId = $this->callbackData['transactionId'] ?? null;

        if (!$transactionId) {
            throw new \InvalidArgumentException('Empty transaction id data');
        }

        $transaction = Transaction::model()->whereAttributes([
            'id' => $transactionId,
        ])->first();

        if (!$transaction) {
            throw new \InvalidArgumentException('Transaction not found');
        }

        if ($transaction->isBitrewardsPayout() && $transaction->isPending()) {
            $status = $this->requestData['status'] ?? null;
            $tx_hash = $this->requestData['tx_id'] ?? null;

            \DB::beginTransaction();

            $transaction->data = array_replace_recursive(TransactionData::make($transaction->data)->toArray(), [Transaction::DATA_TREASURY_TX_HASH => $tx_hash]);
            $transaction->save();

            if (TransactionCallbackRequest::STATUS_CONFIRMED === $status) {
                app(TransactionService::class)->forceConfirm($transaction);
            } elseif (TransactionCallbackRequest::STATUS_REJECTED === $status) {
                app(TransactionService::class)->forceReject($transaction);
            } else {
                throw new \InvalidArgumentException('Unexpected request status');
            }
            \DB::commit();
        }
    }

    protected function handleTokenTransferCallback()
    {
        $partnerId = $this->callbackData['partnerId'] ?? null;

        if (!$partnerId) {
            throw new \InvalidArgumentException('Empty partner id data');
        }

        $eventData = new StoreEventData(StoreEvent::ACTION_REFILL_BIT);
        $eventData->data = $this->requestData;

        $userService = app(UserService::class);

        $user = $userService->findByKey($this->requestData['treasury_data'] ?? null);

        if (!$user) {
            $user = $userService->findByBitAddress($this->requestData['treasury_sender_address'] ?? null);
        }

        if ($user) {
            $eventData->data['userCrmKey'] = $user->key;

            $partner = Partner::model()->whereAttributes([
                'id' => $partnerId,
            ])->first();

            app(StoreEventService::class)->saveEvent($partner, $eventData);
        }
    }

    /**
     * @param $amount
     * @param Partner $partner
     *
     * @throws \Exception
     */
    protected function _sendBitToPartner($amount, Partner $partner)
    {
        try {
            $withdrawal = app(TreasuryClient::class,
                ['userId' => null, 'apiKey' => config('treasury.exchange_api_key')])
                ->withdraw(
                    $partner->eth_address,
                    $amount,
                    \HCurrency::CURRENCY_BIT,
                    config('treasury.exchange_withdraw_key')
                );

            if (!$withdrawal->amount) {
                throw new TreasuryException('Failed to withdraw');
            }
        } catch (TreasuryException $e) {
            throw new \Exception('Treasury exception: '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    protected function _exchangeStoreEvent(EthTransfer $transfer)
    {
        $eventData = new StoreEventData(StoreEvent::ACTION_EXCHANGE_ETH_TO_BIT);

        $eventData->data = [
            StoreEventData::DATA_KEY_TREASURY_ETH_AMOUNT => $transfer->data[EthTransfer::DATA_AMOUNT],
            EthTransfer::DATA_AMOUNT => $transfer->data[EthTransfer::DATA_EXCHANGE_BIT_AMOUNT],
            StoreEventData::DATA_KEY_TREASURY_TX_HASH => $transfer->tx_hash,
            StoreEventData::DATA_KEY_TREASURY_SENDER_ADDRESS => $transfer->from_address,
        ];

        $user = $transfer->receiverUser;

        $eventData->data['userCrmKey'] = $user->key;
        app(StoreEventService::class)->saveEvent($user->partner, $eventData);
//        app(UserService::class)->recalculateBalance($user);
    }

    /**
     * @param EthTransfer $transfer
     *
     * @throws \Exception
     * @throws \Throwable
     */
    protected function _processEthTransfer(EthTransfer $transfer)
    {
        $validTransfer = $transfer->to_address === config('treasury.exchange_address')
            && (
                $transfer->from_address === $transfer->receiverUser->eth_sender_address
                || (TreasuryClient::ROLE_PARTNER === $transfer->receiverUser->role
                    && $transfer->from_address === $transfer->receiverUser->partner->eth_address)
            );

        if (!$validTransfer) {
            \Log::error('Invalid ETH transfer', [$transfer, $transfer->receiverUser]);

            return;
        }

        \DB::beginTransaction();

        try {
            $eth_to_bit = app(FiatService::class)->getExchangeRate('ETH', 'BIT');

            $exchangeBitAmount = number_format($transfer->data[EthTransfer::DATA_AMOUNT] * $eth_to_bit, 18, '.', '');

            $transfer->data = array_replace_recursive($transfer->data->toArray(), [
                EthTransfer::DATA_EXCHANGE_BIT_AMOUNT => $exchangeBitAmount,
            ]);
            $transfer->status = EthTransfer::STATUS_PROCESSING;
            $transfer->save();

            \DB::commit();
        } catch (\Throwable $e) {
            \DB::rollBack();
            \Log::error('Failed to process ETH transfer: '.$e->getMessage(), [$e, $transfer]);

            return;
        }

        $this->_sendBitToPartner($exchangeBitAmount, $transfer->receiverUser->partner);

        \DB::transaction(function () use ($transfer) {
            switch ($transfer->receiverUser->role) {
                case TreasuryClient::ROLE_PARTNER:
                    // TODO: SEND_ALERT_HERE
                    break;

                default:
                    $this->_exchangeStoreEvent($transfer);

                    break;
            }

            $transfer->status = EthTransfer::STATUS_PROCESSED;
            $transfer->save();
        });
    }

    /**
     * @throws \Throwable
     */
    public function handleEthTransferCallback()
    {
        $request = $this->requestData;

        $receiver = $request['treasury_receiver_address'] ?? null;

        if ($receiver !== config('treasury.exchange_address')) {
            // skip
            \Log::error('Receiver address should be the same as exchange address', [$receiver, config('treasury.exchange_address')]);

            return;
        }

        $user = app(UserService::class)->findByEthAddress($request['treasury_sender_address'] ?? null);

        if (!$user) {
            /* @var Partner $partner */
            $partner = app(PartnerService::class)->findByEthAddress($request['treasury_sender_address'] ?? null);

            if ($partner) {
                $user = $partner->mainAdministrator;
            }
        }

        if (!$user) {
            // TODO: SEND_ALERT_HERE
            //

            \Log::error('Sender user not found', [$request]);

            return;
        }

        \DB::beginTransaction();

        try {
            $transfer = new EthTransfer();
            $transfer->from_address = $request['treasury_sender_address'];
            $transfer->to_address = $request['treasury_receiver_address'];
            $transfer->tx_hash = $request['treasury_tx_hash'];

            $transfer->data = [
                EthTransfer::DATA_AMOUNT => $request['amount'],
            ];
            $transfer->receiver_user_id = $user->id;

            $transfer->save();

            \DB::commit();
        } catch (\Throwable $e) {
            \DB::rollBack();
            \Log::error('Unable to process ethTransferCallback: '.$e->getMessage(), [$e, $request]);

            return;
        }

        $this->_processEthTransfer($transfer);
    }

    public function handle()
    {
        try {
            if ('withdraw' === $this->type) {
                $this->handleWithdrawalCallback();
            } elseif ('token-transfer' === $this->type) {
                $this->handleTokenTransferCallback();
            } elseif ('eth-transfer' === $this->type) {
                $this->handleEthTransferCallback();
            } else {
                throw new \InvalidArgumentException('Unexpected request type');
            }
        } catch (\Throwable $e) {
            \Log::error('Unable to process treasury callback. '.$e->getMessage(), [$this->type, $this->requestData, $this->callbackData]);
        }
    }
}
