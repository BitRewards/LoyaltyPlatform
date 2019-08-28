<?php

namespace App\DTO;

use App\DTO\PartnerPage\ActionData;
use App\DTO\PartnerPage\PartnerData;
use App\DTO\PartnerPage\RewardData;
use App\DTO\PartnerPage\UserData;
use App\DTO\PartnerPage\ViewData;
use App\Models\HelpItem;

class PartnerPageData
{
    /**
     * @var bool
     */
    public $isDemoPage;

    /**
     * @var PartnerData
     */
    public $partner;

    /**
     * @var UserData|null
     */
    public $user;

    /**
     * @var ActionData[]
     */
    public $actions;

    /**
     * @var RewardData[]
     */
    public $rewards;

    /**
     * @var TransactionData[]
     */
    public $transactions;

    /**
     * @var TransactionData[]
     */
    public $rewardedTransactions;

    /**
     * @var TransactionData[]
     */
    public $bitrewardsPayoutTransactions;

    /**
     * @var TransactionData[]
     */
    public $depositTransactions;

    /**
     * @var HelpItem[]
     */
    public $helpItems;

    /**
     * @var array
     */
    public $couponList;

    /**
     * @var int
     */
    public $activeCouponCount;

    /**
     * @var ReferrerBalanceData
     */
    public $referrerBalance;

    /**
     * @var ViewData
     */
    public $viewData;

    public function getAvailableRewards(): array
    {
        return array_filter($this->rewards, function (RewardData $rewardData) {
            return $rewardData->price <= ($this->user ? $this->user->balanceAmount : 0);
        });
    }

    public function getAvailableRewardsCount(): int
    {
        return \count($this->getAvailableRewards());
    }
}
