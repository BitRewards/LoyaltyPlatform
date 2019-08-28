<?php

namespace App\Rabbit\Handler;

use App\Services\PartnerService;
use GL\Rabbit\DTO\RPC\CRM\GetLoyaltyPartnerDataRequest;
use GL\Rabbit\DTO\RPC\CRM\GetLoyaltyPartnerDataResponse;

class GetLoyaltyPartnerDataHandler
{
    /**
     * @var PartnerService
     */
    protected $partnerService;

    public function __construct(
        PartnerService $partnerService
) {
        $this->partnerService = $partnerService;
    }

    public function handle(GetLoyaltyPartnerDataRequest $request): GetLoyaltyPartnerDataResponse
    {
        $partner = $this->partnerService->getByGiftdId($request->giftdPartnerId);

        $referralActionProcessor = $partner->getOrderReferralActionProcessor();
        $cashbackActionProcessor = $partner->getOrderCashbackActionProcessor();

        return new GetLoyaltyPartnerDataResponse(
            $partner->getAuthMethod(),
            $partner->isBitrewardsEnabled(),
            $referralActionProcessor ? $referralActionProcessor->getAction()->value_type : null,
            $referralActionProcessor ? ((float) $referralActionProcessor->getAction()->value) : null,

            $referralActionProcessor ? $referralActionProcessor->getReferralRewardValueType() : null,
            $referralActionProcessor ? ((float) $referralActionProcessor->getReferralRewardValue() ?: null) : null,
            $referralActionProcessor ? ((float) $referralActionProcessor->getReferralRewardValueMinAmountTotal() ?: null) : null,

            $cashbackActionProcessor ? (float) $cashbackActionProcessor->getAction()->value : null,
            $cashbackActionProcessor ? $cashbackActionProcessor->getAction()->value_type : null
        );
    }
}
