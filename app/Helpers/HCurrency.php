<?php

class HCurrency
{
    const CURRENCY_ETH = 0;
    const CURRENCY_BIT = 1;

    public static function getAll()
    {
        $titles = [
            self::CURRENCY_ETH => __('ETH'),
            self::CURRENCY_BIT => __('BIT'),
        ];

        return $titles;
    }

    public static function getValues()
    {
        return [self::CURRENCY_ETH, self::CURRENCY_BIT];
    }
}
