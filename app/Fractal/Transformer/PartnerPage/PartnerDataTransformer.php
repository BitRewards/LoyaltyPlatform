<?php

namespace App\Fractal\Transformer\PartnerPage;

use App\DTO\PartnerPage\PartnerData;
use League\Fractal\TransformerAbstract;

class PartnerDataTransformer extends TransformerAbstract
{
    public function transform(PartnerData $partnerData): array
    {
        return [
            'key' => $partnerData->key,
            'url' => $partnerData->url,
            'title' => $partnerData->title,
            'ethAddress' => $partnerData->ethAddress,
            'authMethod' => $partnerData->authMethod,
            'isBitRewardEnabled' => $partnerData->isBitRewardEnabled,
            'currencyId' => $partnerData->currencyId,
            'isOrderReferralActionExist' => $partnerData->isOrderReferralActionExist,
            'clientReferralHeading' => \strip_tags($partnerData->clientReferralHeading),
            'clientReferralSubtitle' => \strip_tags($partnerData->clientReferralSubtitle),
            'clientReferralMinAmountNotification' => $partnerData->clientReferralMinAmountNotification,
            'discountInsteadOfLoyalty' => $partnerData->discountInsteadOfLoyalty,
            'signUpBonusAmount' => $partnerData->signUpBonusAmount,
            'signUpBonus' => $partnerData->signUpBonus,
            'fiatReferralBonus' => $partnerData->fiatReferralBonus,
            'primaryColor' => $partnerData->primaryColor,
            'isMenuFontBolder' => $partnerData->isMenuFontBolder,
            'brwToUsdRate' => $partnerData->brwToUsdRate,
            'brwAmount' => $partnerData->brwAmount,
            'withdrawFeeType' => $partnerData->withdrawFeeType,
            'withdrawFeeValue' => $partnerData->withdrawFeeValue,
            'withdrawAmountMin' => $partnerData->withdrawAmountMin,
            'withdrawAmountMax' => $partnerData->withdrawAmountMax,
        ];
    }
}
