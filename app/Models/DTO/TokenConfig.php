<?php

namespace App\Models\DTO;

use App\DTO\DTO;

class TokenConfig extends DTO
{
    const FORMAT_SHORT_NUMERIC = 'short-numeric';
    const FORMAT_NUMERIC_8_DIGIT = 'numeric-8-digit';
    const FORMAT_DEFAULT = 'default';

    public $lifetime;
    public $format;
    public $exclusive;

    public static function makeShortLivingNumericToken()
    {
        return static::make(['format' => TokenConfig::FORMAT_SHORT_NUMERIC, 'lifetime' => 10 * 60]);
    }

    public static function makeTokenForEmailAuth()
    {
        return static::make(['format' => TokenConfig::FORMAT_SHORT_NUMERIC, 'lifetime' => 5 * 60])->setExclusive(true);
    }

    public static function makeOneDayToken()
    {
        return static::make(['format' => TokenConfig::FORMAT_DEFAULT, 'lifetime' => 24 * 3600]);
    }

    public static function makeLongLivingToken()
    {
        return static::make(['format' => TokenConfig::FORMAT_DEFAULT, 'lifetime' => null]);
    }

    public function setExclusive($exclusive)
    {
        $this->exclusive = $exclusive;

        return $this;
    }
}
