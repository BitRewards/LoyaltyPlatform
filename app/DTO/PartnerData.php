<?php

namespace App\DTO;

class PartnerData extends DTO
{
    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $image;

    /**
     * @var PartnerSettingsData
     */
    public $settings;

    /**
     * @var string
     */
    public $ethAddress;

    public function __construct()
    {
        $this->settings = new PartnerSettingsData();
    }
}
