<?php

class HEthereum
{
    public static function toWei($amount)
    {
        return number_format($amount * 1e18, 0, '.', '');
    }

    public static function fromWei($amountInWei)
    {
        return number_format($amountInWei * (1e-18), 18, '.', '');
    }
}
