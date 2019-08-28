<?php

namespace App\Rabbit\Handler;

use App\Services\PartnerService;
use GL\Rabbit\DTO\Events\PartnerUpdate;

class PartnerUpdateHandler
{
    /**
     * @var PartnerService
     */
    protected $partnerService;

    public function __construct(PartnerService $partnerService)
    {
        $this->partnerService = $partnerService;
    }

    public function handle(PartnerUpdate $event)
    {
        \DB::transaction(function () use ($event) {
            $partner = $this->partnerService->getByGiftdId($event->giftdPartnerId);

            if (!$partner) {
                return;
            }
            $partner->currency = $event->currency;
            $partner->default_language = $event->defaultLanguage ?: $partner->default_language;
            $partner->default_country = $event->defaultCountry ?: $partner->default_country;
            $partner->url = $event->url;
            $partner->email = $event->email;
            $partner->saveOrFail();
        });
    }
}
