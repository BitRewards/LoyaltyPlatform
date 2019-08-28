<?php
/**
 * MockApiClient.php
 * Creator: lehadnk
 * Date: 23/07/2018.
 */

namespace App\Services\Treasury;

use App\DTO\DTO;

class MockApiClient implements IApiClient
{
    public $useEcho = false;

    public function withdraw($address, $amount, $currency, $withdraw_key, $callback_url = null): DTO
    {
        if ($this->useEcho) {
            echo 'Treasury operation: withdraw'.PHP_EOL;
            print_r(\func_get_args());
        }

        return new DTO();
    }

    public function getEthTransferFeeEstimate()
    {
        if ($this->useEcho) {
            echo 'Treasury operation: getEthTransferFeeEstimate'.PHP_EOL;
        }
    }

    public function getTokenTransferEthFeeEstimate()
    {
        if ($this->useEcho) {
            echo 'Treasury operation: getTokenTransferEthFeeEstimate'.PHP_EOL;
        }
    }

    public function getBalance()
    {
        if ($this->useEcho) {
            echo 'Treasury operation: getBalance'.PHP_EOL;
        }
    }

    public function listTransactions()
    {
        if ($this->useEcho) {
            echo 'Treasury operation: listTransactions'.PHP_EOL;
        }
    }

    public function createWallet()
    {
        if ($this->useEcho) {
            echo 'Treasury operation: createWallet'.PHP_EOL;
        }
    }
}
