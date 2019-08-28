<?php

use App\Models\Partner;

class HContext
{
    private static $currentPartnersStack;

    public static function setPartner(Partner $partner)
    {
        self::$currentPartnersStack[] = $partner;
    }

    /**
     * @return Partner|null
     */
    public static function getPartner()
    {
        return self::$currentPartnersStack ? end(self::$currentPartnersStack) : null;
    }

    public static function restorePartner()
    {
        array_pop(self::$currentPartnersStack);
    }

    public static function isBitrewardsEnabled()
    {
        $partner = self::getPartner();

        return $partner && $partner->isBitrewardsEnabled();
    }
}
