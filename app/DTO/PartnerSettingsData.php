<?php

namespace App\DTO;

class PartnerSettingsData
{
    /**
     * @var string
     */
    public $minWithdraw;

    /**
     * @var string
     */
    public $maxWithdraw;

    /**
     * @var int
     */
    public $withdrawFeeAmount;

    /**
     * @var string
     */
    public $withdrawFeeType;

    /**
     * @var string
     */
    public $fiatWithdrawFeeType;

    /**
     * @var string
     */
    public $fiatWithdrawFeeAmount;

    /**
     * @var string
     */
    public $fiatWithdrawFee;

    /**
     * @var string
     */
    public $fiatMinWithdraw;

    /**
     * @var float
     */
    public $fiatMinWithdrawAmount;

    /**
     * @var string
     */
    public $fiatMaxWithdraw;

    /**
     * @var float
     */
    public $fiatMaxWithdrawAmount;
}
