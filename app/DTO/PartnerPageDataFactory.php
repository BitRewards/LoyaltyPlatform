<?php

namespace App\DTO;

use App\DTO\Factory\SavedCouponFactory;
use App\DTO\PartnerPage\ActionDataFactory;
use App\DTO\PartnerPage\HelpItemDataFactory;
use App\DTO\PartnerPage\PartnerDataFactory;
use App\DTO\PartnerPage\RewardDataFactory;
use App\DTO\PartnerPage\TransactionDataFactory;
use App\DTO\PartnerPage\UserDataFactory;
use App\DTO\PartnerPage\ViewDataFactory;
use App\Models\Partner;
use App\Models\User;
use App\Services\ReferralStatisticService;
use App\Services\TransactionService;
use Illuminate\Support\Collection;

class PartnerPageDataFactory
{
    /**
     * @var UserDataFactory
     */
    protected $userDataFactory;

    /**
     * @var PartnerDataFactory
     */
    protected $partnerDataFactory;

    /**
     * @var ActionDataFactory
     */
    protected $actionDataFactory;

    /**
     * @var RewardDataFactory
     */
    protected $rewardDataFactory;

    /**
     * @var TransactionDataFactory
     */
    protected $transactionDataFactory;

    /**
     * @var HelpItemDataFactory
     */
    protected $helpItemDataFactory;

    /**
     * @var ViewDataFactory
     */
    protected $viewDataFactory;

    /**
     * @var SavedCouponFactory
     */
    protected $savedCouponFactory;

    /**
     * @var ReferralStatisticService
     */
    protected $referralStatisticService;

    /**
     * @var TransactionService
     */
    protected $transactionService;

    public function __construct(
        UserDataFactory $userDataFactory,
        PartnerDataFactory $partnerDataFactory,
        ActionDataFactory $actionDataFactory,
        RewardDataFactory $rewardDataFactory,
        TransactionDataFactory $transactionDataFactory,
        HelpItemDataFactory $helpItemDataFactory,
        ViewDataFactory $viewDataFactory,
        SavedCouponFactory $savedCouponFactory,
        ReferralStatisticService $referralStatisticService,
        TransactionService $transactionService
    ) {
        $this->userDataFactory = $userDataFactory;
        $this->partnerDataFactory = $partnerDataFactory;
        $this->actionDataFactory = $actionDataFactory;
        $this->rewardDataFactory = $rewardDataFactory;
        $this->transactionDataFactory = $transactionDataFactory;
        $this->helpItemDataFactory = $helpItemDataFactory;
        $this->viewDataFactory = $viewDataFactory;
        $this->savedCouponFactory = $savedCouponFactory;
        $this->referralStatisticService = $referralStatisticService;
        $this->transactionService = $transactionService;
    }

    public function factory(
        Partner $partner,
        User $user = null,
        string $overriddenTitle = null,
        array $tags = []
    ): PartnerPageData {
        $partnerPage = new PartnerPageData();
        $partnerPage->isDemoPage = null !== $overriddenTitle;

        if ($user) {
            $partnerPage->user = $this->userDataFactory->factory($user, $partner);
            $partnerPage->transactions = $this->transactionDataFactory->factoryUserTransactions($partner, $user);
            $partnerPage->rewardedTransactions = $this->transactionDataFactory->factoryRewardedUserTransactions($partner, $user);
            $partnerPage->bitrewardsPayoutTransactions = $this->transactionDataFactory->factoryBitrewardsPayoutTransactions($partner, $user);
            $partnerPage->depositTransactions = $this->transactionDataFactory->factoryDepositTransactions($partner, $user);
            $partnerPage->couponList = (new Collection())
                ->merge($this->savedCouponFactory->factoryPartnerPageUserSavedCoupons($user))
                ->merge($partnerPage->rewardedTransactions)
                ->sortByDesc(function ($coupon) {
                    /* @var SavedCouponData|PartnerPage\TransactionData $coupon */
                    return $coupon->createdAt;
                })
                ->all();

            $partnerPage->activeCouponCount = $this
                ->transactionService
                ->getActiveUserDiscountTransactionsCount($user);

            if ($partner->isFiatReferralEnabled()) {
                $partnerPage->referrerBalance = $this->referralStatisticService->getReferrerBalance($user);
            }
        }

        $partnerPage->partner = $this->partnerDataFactory->factory($partner);
        $partnerPage->actions = $this->actionDataFactory->factory($partner, $user, $tags);
        $partnerPage->rewards = $this->rewardDataFactory->factory($partner, $user, $tags);
        $partnerPage->helpItems = $this->helpItemDataFactory->factory($partner);
        $partnerPage->viewData = $this->viewDataFactory->factory($partner, $overriddenTitle);

        return $partnerPage;
    }
}
