<?php

namespace App\Services\Treasury;

use App\DTO\DTO;
use App\DTO\TreasuryWithdrawalData;
use Graze\GuzzleHttp\JsonRpc\Client;

class ApiClient implements IApiClient
{
    private $userId;
    private $apiKey;
    private $baseUrl;

    private $_client;

    public function __construct($userId, $apiKey)
    {
        $this->userId = $userId;
        $this->apiKey = $apiKey;
        $this->baseUrl = config('treasury.api_base_url');

        $this->_client = Client::factory($this->baseUrl);
    }

    /**
     * @param $method
     * @param $params
     *
     * @return mixed
     *
     * @throws TreasuryException
     * @throws TreasuryLowBalanceException
     */
    protected function queryApi($method, $params)
    {
        try {
            $response = $this->_client->send($this->_client->request(1, $method, $params));

            if ($response->getRpcErrorCode()) {
                $errorData = $response->getRpcErrorData();
                $internalError = $errorData['previous']['data'] ?? [];

                switch ($internalError['code']) {
                    case TreasuryException::CODE_NOT_ENOUGH_FUNDS:
                        throw new TreasuryLowBalanceException();

                        break;

                    default:
                        throw new TreasuryException(json_encode($errorData));

                        break;
                }
            }

            $result = $response->getRpcResult();

            return $result;
        } catch (\Throwable $e) {
            if ($e instanceof TreasuryException) {
                throw $e;
            } else {
                throw new TreasuryException($e->getMessage());
            }
        }
    }

    /**
     * @return mixed
     *
     * @throws TreasuryException
     */
    public function createWallet()
    {
        return $this->queryApi('v1.wallet.create-wallet', [
            'crm_user_id' => $this->userId,
            'api_key' => $this->apiKey,
            'callback_url' => route('api.treasury.token.callback', [], true),
        ]);
    }

    /**
     * @return mixed
     *
     * @throws TreasuryException
     */
    public function listTransactions()
    {
        return $this->queryApi('v1.wallet.list-transactions', [
            'api_key' => $this->apiKey,
        ]);
    }

    /**
     * @return mixed
     *
     * @throws TreasuryException
     */
    public function getBalance()
    {
        return $this->queryApi('v1.wallet.get-balance', [
            'api_key' => $this->apiKey,
        ]);
    }

    /**
     * @return mixed
     *
     * @throws TreasuryException
     * @throws TreasuryLowBalanceException
     */
    public function getTokenTransferEthFeeEstimate()
    {
        return $this->queryApi('v1.wallet.token-transfer-eth-fee-estimate', [
            'api_key' => $this->apiKey,
        ]);
    }

    /**
     * @return mixed
     *
     * @throws TreasuryException
     * @throws TreasuryLowBalanceException
     */
    public function getEthTransferFeeEstimate()
    {
        return $this->queryApi('v1.wallet.eth-transfer-fee-estimate', [
            'api_key' => $this->apiKey,
        ]);
    }

    /**
     * @param $address
     * @param $amount
     * @param $currency
     * @param $withdraw_key
     * @param $callback_url
     *
     * @return mixed
     *
     * @throws TreasuryException
     * @throws TreasuryLowBalanceException
     */
    public function withdraw($address, $amount, $currency, $withdraw_key, $callback_url = null): DTO
    {
        $response = $this->queryApi('v1.wallet.withdraw', [
            'api_key' => $this->apiKey,
            'dest_address' => $address,
            'amount' => \HEthereum::toWei($amount),
            'currency' => $currency,
            'withdraw_key' => $withdraw_key,
            'callback_url' => $callback_url,
        ]);

        return TreasuryWithdrawalData::make($response);
    }
}
