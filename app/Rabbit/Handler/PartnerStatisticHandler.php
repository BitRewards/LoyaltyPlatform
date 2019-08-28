<?php

namespace App\Rabbit\Handler;

use App\Models\Partner;
use App\Rabbit\Validator\PartnerStatisticRequestValidator;
use App\Rabbit\Validator\Traits\ValidatorAwareTrait;
use App\Services\PartnerStatisticService;
use GL\Rabbit\DTO\RPC\CRM\PartnerStatisticRequest;
use GL\Rabbit\DTO\RPC\CRM\PartnerStatisticResponse;

class PartnerStatisticHandler
{
    use ValidatorAwareTrait;

    /**
     * @var PartnerStatisticService
     */
    protected $partnerStatisticService;

    public function __construct(
        PartnerStatisticService $partnerStatisticService,
        PartnerStatisticRequestValidator $validator
    ) {
        $this->partnerStatisticService = $partnerStatisticService;
        $this->setValidator($validator);
    }

    public function handle(PartnerStatisticRequest $request): PartnerStatisticResponse
    {
        $this->getValidator()->validateOrFail($request);

        $partner = Partner::where('giftd_id', $request->getGiftdPartnerId())->first();

        if (!$partner) {
            throw new \LogicException('Partner not found');
        }

        return $this->partnerStatisticService->getStatistic($partner, $request->getFrom(), $request->getTo());
    }
}
