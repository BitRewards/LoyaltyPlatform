<?php
/**
 * IApiClient.php
 * Creator: lehadnk
 * Date: 23/07/2018.
 */

namespace App\Services\Treasury;

use App\DTO\DTO;

interface IApiClient
{
    public function withdraw($address, $amount, $currency, $withdraw_key, $callback_url = null): DTO;

    public function getEthTransferFeeEstimate();

    public function getTokenTransferEthFeeEstimate();

    public function getBalance();

    public function listTransactions();

    public function createWallet();
}
