<?php

namespace App\DTO;

/**
 * @property float  $fiatWithdrawAmount
 * @property float  $fiatWithdrawFee
 * @property string $fiatWithdrawFeeType
 * @property float  $fiatWithdrawFeeValue
 * @property string $fiatWithdrawCardNumber
 * @property string $fiatWithdrawFirstName
 * @property string $fiatWithdrawLastName
 * @property int    $fiatWithdrawExternalOperationId
 * @property float  $fiatWithdrawMerchantBalanceBefore
 * @property float  $fiatWithdrawMerchantBalanceAfter
 */
class TransactionData extends DTO
{
    public $notUsableByClient;
    public $lifetimeOverridden;
    public $promo_code;
    public $expires;
    public $url;
    public $comment;
    public $ethereumAddress;
    public $bitrewardsPayoutAmount;
    public $bitrewardsWithdrawFee;
    public $bitrewardsWithdrawFeeType;
    public $bitrewardsWithdrawFeeValue;
}
