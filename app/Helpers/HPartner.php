<?php

use App\Models\Partner;

class HPartner
{
    public static function getOrderReferralRewardStr(Partner $partner, $useCurrencySign = true)
    {
        $action = $partner->findActionByType(App\Models\Action::TYPE_ORDER_REFERRAL);

        if (!$action) {
            return null;
        }

        $processor = $action->getActionProcessor();
        /**
         * @var App\Services\ActionProcessors\OrderReferral
         */
        $referralRewardStr = HAction::getReferralRewardStr($processor, $useCurrencySign);

        return $referralRewardStr;
    }

    public static function hasUSDCurrency(Partner $partner)
    {
        return HAmount::CURRENCY_USD === $partner->currency;
    }
}
