<?php

namespace App\DTO\Factory;

use App\DTO\PartnerData;
use App\Models\Partner;

class PartnerFactory
{
    /**
     * @var \HCustomizations
     */
    protected $customizationHelper;

    /**
     * @var PartnerSettingsFactory
     */
    protected $partnerSettingsFactory;

    public function __construct(
        \HCustomizations $customizationHelper,
        PartnerSettingsFactory $partnerSettingsFactory
    ) {
        $this->customizationHelper = $customizationHelper;
        $this->partnerSettingsFactory = $partnerSettingsFactory;
    }

    public function factory(Partner $partner): PartnerData
    {
        $partnerData = new PartnerData();
        $partnerData->key = $partner->key;
        $partnerData->title = $partner->title;
        $partnerData->image = $this->customizationHelper::logoPicture($partner);
        $partnerData->ethAddress = $partner->eth_address;
        $partnerData->settings = $this->partnerSettingsFactory->factory($partner);

        return $partnerData;
    }
}
