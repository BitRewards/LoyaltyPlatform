<?php

namespace App\DTO\PartnerPage;

use App\Models\Partner;
use App\Services\HelpService;

class HelpItemDataFactory
{
    /**
     * @var HelpService
     */
    protected $helpService;

    public function __construct(HelpService $helpService)
    {
        $this->helpService = $helpService;
    }

    public function factory(Partner $partner): array
    {
        $result = [];

        foreach ($this->helpService->helpItemsForLanguage($partner) as $helpItem) {
            $helpItemData = new HelpItemData();
            $helpItemData->question = $helpItem->question;
            $helpItemData->answer = $helpItem->answer;

            $result[] = $helpItemData;
        }

        return $result;
    }
}
