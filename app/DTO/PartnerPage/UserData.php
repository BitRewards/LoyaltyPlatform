<?php

namespace App\DTO\PartnerPage;

class UserData
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $phone;

    /**
     * @var string
     */
    public $avatar;

    /**
     * @var string
     */
    public $referralLink;

    /**
     * @var string
     */
    public $referralPromoCode;

    /**
     * @var string
     */
    public $bitTokenSenderAddress;

    /**
     * @var UserCodeData[]
     */
    public $codes = [];

    /**
     * @var float|int
     */
    public $balanceAmount;

    /**
     * @var string
     */
    public $balanceAmountPercent;

    /**
     * @var string
     */
    public $currency;

    /**
     * @var string
     */
    public $balanceInPartnerCurrency;

    /**
     * @var UserViewData
     */
    public $viewData;

    /**
     * @var bool
     */
    public $isUserWithoutEmailOrPhone;

    /**
     * @var bool
     */
    public $isUserConfirmed;

    /**
     * @var string
     */
    public $ethSenderAddress;

    /**
     * @var string
     */
    public $key;

    public function getUserTitle()
    {
        return $this->name ?? $this->email ?? $this->phone ?? null;
    }

    public function getBalance(): string
    {
        return "{$this->balanceAmount} {$this->currency}";
    }
}
