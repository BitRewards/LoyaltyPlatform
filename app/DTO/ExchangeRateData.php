<?php

namespace App\DTO;

class ExchangeRateData extends DTO
{
    /**
     * @var string
     */
    public $currency;

    /**
     * @var array
     */
    public $exchangeRate = [];
}
