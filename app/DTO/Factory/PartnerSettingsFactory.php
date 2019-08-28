<?php

namespace App\DTO\Factory;

use App\DTO\PartnerSettingsData;
use App\Models\Partner;
use App\Services\PartnerService;
use Illuminate\Support\Facades\Auth;

class PartnerSettingsFactory
{
    /**
     * @var Auth
     */
    protected $auth;

    /**
     * @var PartnerService
     */
    protected $partnerService;

    /**
     * @var \HAmount
     */
    protected $amountHelper;

    public function __construct(Auth $auth, PartnerService $partnerService, \HAmount $amountHelper)
    {
        $this->auth = $auth;
        $this->partnerService = $partnerService;
        $this->amountHelper = $amountHelper;
    }

    public function factory(Partner $partner): PartnerSettingsData
    {
        $withdrawMinAmount = $partner->getBitWithdrawMinAmount();
        $withdrawMaxAmount = $this
            ->partnerService
            ->getBitWithdrawMaxAmount($partner, $this->auth::user());

        $settings = new PartnerSettingsData();
        $settings->minWithdraw = $this->amountHelper::points($withdrawMinAmount, $partner);
        $settings->maxWithdraw = $this->amountHelper::points($withdrawMaxAmount, $partner);
        $settings->withdrawFeeAmount = $partner->getBitWithdrawFee();
        $settings->withdrawFeeType = $partner->getBitWithdrawFeeType();
        $settings->fiatWithdrawFeeType = $partner->getFiatWithdrawFeeType();
        $settings->fiatWithdrawFeeAmount = $partner->getFiatWithdrawFee();
        $settings->fiatWithdrawFee = $this->amountHelper::getFiatWithdrawFeeStr($partner);
        $settings->fiatMinWithdrawAmount = $partner->getFiatWithdrawMinAmount();
        $settings->fiatMinWithdraw = $this->amountHelper::fMedium($settings->fiatMinWithdrawAmount, $partner->currency);
        $settings->fiatMaxWithdrawAmount = $partner->getFiatWithdrawMaxAmount();
        $settings->fiatMaxWithdraw = $this->amountHelper::fMedium($settings->fiatMaxWithdrawAmount, $partner->currency);

        return $settings;
    }
}
