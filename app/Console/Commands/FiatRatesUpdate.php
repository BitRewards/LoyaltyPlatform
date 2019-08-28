<?php

namespace App\Console\Commands;

use App\Services\Fiat\FiatService;
use Illuminate\Console\Command;

class FiatRatesUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fiat:update-rates';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fiatService = app(FiatService::class);
        array_map(function ($currency) use ($fiatService) {
            $value = $fiatService->getUSDRate($currency, $forceUpdate = true);
            echo $currency.'='.$value.PHP_EOL;
        }, array_merge($fiatService::getCurrencyList(), $fiatService::$COIN_LIST));

//        $currencies = array_map(function ($code) {
//            return \HAmount::sISO4217($code);
//        }, array_keys(\HAmount::getCurrencyList()));
//
//        array_map(function($currency) {
//            app(FiatService::class)->getRate($currency, $forceUpdate = true);
//        }, $currencies);
    }
}
