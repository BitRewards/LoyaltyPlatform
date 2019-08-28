<?php

namespace App\DTO\PartnerPage;

use App\DTO\SavedCouponData as BasedSavedCouponData;

class SavedCouponData extends BasedSavedCouponData
{
    /**
     * @var string
     */
    public $modalUrl;

    /**
     * @var string
     */
    public $created;

    /**
     * @var string
     */
    public $expired;
}
