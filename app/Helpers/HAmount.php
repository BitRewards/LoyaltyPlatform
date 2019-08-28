<?php

use App\Models\Partner;
use App\Services\Fiat\FiatService;

class HAmount
{
    const DECIMALS = 2;

    const SHORTEN_THRESHOLD = 1000000;

    const CURRENCY_RUB = 1;
    const CURRENCY_KZT = 2;
    const CURRENCY_EUR = 3;
    const CURRENCY_UAH = 4;
    const CURRENCY_USD = 5;
    const CURRENCY_GEL = 6;
    const CURRENCY_BYN = 7;
    const CURRENCY_GBP = 8;
    const CURRENCY_TRY = 9;
    const CURRENCY_CNY = 10;

    const ROUBLE_REGULAR = '<span class="rouble-regular"></span>';
    const ROUBLE_BOLD = '<span class="rouble-semibold"></span>';

    const ROUBLES_PER_USD = 50;

    public static function getCurrencyList()
    {
        return [
            self::CURRENCY_RUB => _('Ruble'),
            self::CURRENCY_UAH => _('Hrivna'),
            self::CURRENCY_KZT => _('Tenge'),
            self::CURRENCY_GEL => _('Lari'),
            self::CURRENCY_EUR => _('Euro'),
            self::CURRENCY_USD => _('US Dollar'),
            self::CURRENCY_BYN => _('Belorus ruble'),
            self::CURRENCY_GBP => _('British pound'),
            self::CURRENCY_TRY => _('Turkish Lira'),
            self::CURRENCY_CNY => _('Yuan Renminbi'),
        ];
    }

    public static function isCurrencyPrepended($currency)
    {
        return self::CURRENCY_USD == $currency || self::CURRENCY_EUR == $currency;
    }

    public static function f($amount, $append = '', $noWhitespace = false, $boldAmount = false, $boldCurrency = false, $prependCurrency = false)
    {
        if (null === $amount || 0 == strlen($amount)) {
            return '';
        }
        $amount = floatval($amount);

        if ((int) $amount == $amount) {
            return
                ($prependCurrency ? $append : '').
                ($boldAmount ? '<b>' : '').
                number_format($amount, 0, '.', ($amount >= 10000 && !$noWhitespace) ? ' ' : '').
                (($boldAmount && !$boldCurrency) ? '</b>' : '').
                ((!$boldAmount && $boldCurrency) ? '<b>' : '').
                ($prependCurrency ? '' : $append).
                (($boldCurrency) ? '</b>' : '');
        } else {
            return
                ($prependCurrency ? $append : '').
                ($boldAmount ? '<b>' : '').
                number_format($amount, self::DECIMALS, '.', ($amount >= 10000 && !$noWhitespace) ? ' ' : '').
                (($boldAmount && !$boldCurrency) ? '</b>' : '').
                ((!$boldAmount && $boldCurrency) ? '<b>' : '').
                ($prependCurrency ? '' : $append).
                (($boldCurrency) ? '</b>' : '');
        }
    }

    public static function novaAmountFormat($amount): string
    {
        return number_format($amount, $amount == (int) $amount ? 0 : 2);
    }

    public static function fSign($amount, $currency)
    {
        return self::f($amount, self::sign($currency), false, false, false, self::isCurrencyPrepended($currency));
    }

    public static function fSignToggle($bold, $amount, $currency)
    {
        return $bold ? self::fSignBold($amount, $currency) : self::fSign($amount, $currency);
    }

    public static function fSignBold($amount, $currency)
    {
        return self::f($amount, self::signBold($currency), false, false, false, self::isCurrencyPrepended($currency));
    }

    public static function sISO4217($currency)
    {
        switch ($currency) {
            case HAmount::CURRENCY_RUB:
                return 'RUB';

                break;

            case HAmount::CURRENCY_KZT:
                return 'KZT';

                break;

            case HAmount::CURRENCY_GEL:
                return 'GEL';

                break;

            case HAmount::CURRENCY_EUR:
                return 'EUR';

                break;

            case HAmount::CURRENCY_USD:
                return 'USD';

                break;

            case HAmount::CURRENCY_GBP:
                return 'GBP';

                break;

            case HAmount::CURRENCY_UAH:
                return 'UAH';

                break;

            case HAmount::CURRENCY_BYN:
                return 'BYN';

                break;

            case HAmount::CURRENCY_TRY:
                return 'TRY';

                break;

            case HAmount::CURRENCY_CNY:
                return 'CNY';

                break;

            default:
                return 'RUB';

                break;
        }
    }

    public static function sISOYandex($currency)
    {
        switch ($currency) {
            case HAmount::CURRENCY_RUB:
                return 'RUR';

                break;

            case HAmount::CURRENCY_KZT:
                return 'KZT';

                break;

            case HAmount::CURRENCY_GEL:
                return 'GEL';

                break;

            case HAmount::CURRENCY_EUR:
                return 'EUR';

                break;

            case HAmount::CURRENCY_USD:
                return 'USD';

                break;

            case HAmount::CURRENCY_GBP:
                return 'GBP';

                break;

            case HAmount::CURRENCY_UAH:
                return 'UAH';

                break;

            case HAmount::CURRENCY_BYN:
                return 'BYN';

                break;

            case HAmount::CURRENCY_TRY:
                return 'TRY';

                break;

            case HAmount::CURRENCY_CNY:
                return 'CNY';

                break;

            default:
                return 'RUR';

                break;
        }
    }

    public static function sShort($currency)
    {
        switch ($currency) {
            case HAmount::CURRENCY_KZT:
                return '₸';

                break;

            case HAmount::CURRENCY_GEL:
                return 'ლ';

                break;

            case HAmount::CURRENCY_EUR:
                return '€';

                break;

            case HAmount::CURRENCY_USD:
                return '$';

                break;

            case HAmount::CURRENCY_GBP:
                return '£';

                break;

            case HAmount::CURRENCY_UAH:
                return '₴';

                break;

            case HAmount::CURRENCY_BYN:
                return 'р.';

                break;

            case HAmount::CURRENCY_TRY:
                return '₺';

                break;

            case HAmount::CURRENCY_CNY:
                return '元';

                break;

            default:
                return 'р.';

                break;
        }
    }

    public static function sMedium($currency)
    {
        switch ($currency) {
            case HAmount::CURRENCY_UAH:
                return 'грн';

                break;

            case HAmount::CURRENCY_KZT:
                return '₸';

                break;

            case HAmount::CURRENCY_GEL:
                return 'ლ';

                break;

            case HAmount::CURRENCY_USD:
                return '$';

                break;

            case HAmount::CURRENCY_GBP:
                return '£';

                break;

            case HAmount::CURRENCY_EUR:
                return '€';

                break;

            case HAmount::CURRENCY_BYN:
                return 'руб.';

                break;

            case HAmount::CURRENCY_TRY:
                return '₺';

                break;

            case HAmount::CURRENCY_CNY:
                return '元';

                break;

            default:
                return 'руб.';

                break;
        }
    }

    public static function sign($currency, $useUtfRoubleSign = false)
    {
        switch ($currency) {
            case HAmount::CURRENCY_KZT:
                return '₸';

                break;

            case HAmount::CURRENCY_GEL:
                return 'ლ';

                break;

            case HAmount::CURRENCY_EUR:
                return '€';

                break;

            case HAmount::CURRENCY_USD:
                return '$';

                break;

            case HAmount::CURRENCY_GBP:
                return '£';

                break;

            case HAmount::CURRENCY_UAH:
                return '₴';

                break;

            case HAmount::CURRENCY_BYN:
                return ' р.';

                break;

            case HAmount::CURRENCY_TRY:
                return '₺';

                break;

            case HAmount::CURRENCY_CNY:
                return '元';

                break;

            default:
                return '₽';

                break;
        }
    }

    public static function signBold($currency)
    {
        switch ($currency) {
            case HAmount::CURRENCY_KZT:
                return '₸';

                break;

            case HAmount::CURRENCY_GEL:
                return 'ლ';

            case HAmount::CURRENCY_EUR:
                return '€';

                break;

            case HAmount::CURRENCY_USD:
                return '$';

                break;

            case HAmount::CURRENCY_GBP:
                return '£';

                break;

            case HAmount::CURRENCY_UAH:
                return '₴';

                break;

            case HAmount::CURRENCY_BYN:
                return ' р.';

                break;

            case HAmount::CURRENCY_TRY:
                return '₺';

                break;

            case HAmount::CURRENCY_CNY:
                return '元';

                break;

            default:
                return '₽';

                break;
        }
    }

    public static function fShort($amount, $currency = self::CURRENCY_RUB, $noWhiteSpace = false)
    {
        switch ($currency) {
            case HAmount::CURRENCY_GEL:
                $currencySign = 'ლ';

                break;

            case HAmount::CURRENCY_KZT:
                $currencySign = '₸';

                break;

            case HAmount::CURRENCY_EUR:
                $currencySign = '€';

                break;

            case HAmount::CURRENCY_USD:
                $currencySign = '$';

                break;

            case HAmount::CURRENCY_GBP:
                $currencySign = '£';

                break;

            case HAmount::CURRENCY_UAH:
                $currencySign = ' грн';

                break;

            case HAmount::CURRENCY_BYN:
                $currencySign = ' р.';

                break;

            case HAmount::CURRENCY_TRY:
                $currencySign = '₺';

                break;

            case HAmount::CURRENCY_CNY:
                $currencySign = '元';

                break;

            default:
                $currencySign = ' р.';

                break;
        }

        return self::f($amount, $currencySign, $noWhiteSpace, false, false, self::isCurrencyPrepended($currency));
    }

    public static function labelMedium($currency)
    {
        switch ($currency) {
            case HAmount::CURRENCY_KZT:
                return '₸';

                break;

            case HAmount::CURRENCY_GEL:
                return 'ლ';

                break;

            case HAmount::CURRENCY_EUR:
                return 'евро';

                break;

            case HAmount::CURRENCY_USD:
                return '$';

                break;

            case HAmount::CURRENCY_GBP:
                return '£';

                break;

            case HAmount::CURRENCY_UAH:
                return 'грн';

                break;

            case HAmount::CURRENCY_BYN:
                return 'руб.';

                break;

            case HAmount::CURRENCY_TRY:
                return '₺';

                break;

            case HAmount::CURRENCY_CNY:
                return '元';

                break;

            default:
                return 'руб.';

                break;
        }
    }

    public static function fMedium($amount, $currency, $noWhiteSpace = false)
    {
        return self::f($amount, ' '.self::labelMedium($currency), $noWhiteSpace);
    }

    public static function fLong($amount, $currency)
    {
        switch ($currency) {
            case HAmount::CURRENCY_KZT:
                $currency = ' '._('Tenge');

                break;

            case HAmount::CURRENCY_EUR:
                $currency = ' '._('Euro');

                break;

            case HAmount::CURRENCY_GEL:
                $currency = ' '._('Lari');

                break;

            case HAmount::CURRENCY_USD:
                $currency = ' '.HLanguage::countForm((int) $amount, _('Dollar'), _('Dollars'), _('Dollars'), false);

                break;

            case HAmount::CURRENCY_GBP:
                $currency = ' '.HLanguage::countForm((int) $amount, _('Pound'), _('Pounds'), _('Pounds'), false);

                break;

            case HAmount::CURRENCY_UAH:
                $currency = ' '.HLanguage::countForm((int) $amount, _('Hrivna'), _('Hrivnas'), _('Hrivnas'), false);

                break;

            case HAmount::CURRENCY_BYN:
                $currency = ' '.HLanguage::countForm((int) $amount, _('ruble'), _('rubles'), _('rubles'), false);

                break;

            case HAmount::CURRENCY_TRY:
                $currency = ' '._('Turkish lira');

                break;

            case HAmount::CURRENCY_CNY:
                $currency = ' '._('Yuan');

                break;

            default:
                $currency = ' '.HLanguage::countForm((int) $amount, _('ruble'), _('rubles'), _('rubles'), false);

                break;
        }

        return self::f($amount, $currency);
    }

    public static function formatExcel($amount, $separator = ',')
    {
        if (0 == strlen($amount)) {
            return '';
        }

        return number_format($amount, 2, $separator, '');
    }

    public static function round($number, $decimals)
    {
        if (0 == $decimals) {
            return round($number);
        }

        return (float) number_format($number, $decimals, '.', '');
    }

    public static function percentage($number, $multiplyBy100 = true)
    {
        if ((int) $number == $number) {
            return ((int) $number).'%';
        }

        return number_format($number * ($multiplyBy100 ? 100 : 1), self::DECIMALS, '.', '').'%';
    }

    /**
     * Возвращает сумму прописью.
     *
     * @author runcore
     *
     * @uses   \morph(...)
     *
     * @param $num
     *
     * @return string
     */
    public static function num2str($num)
    {
        $nul = 'ноль';
        $ten = array(
            array('', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
            array('', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
        );
        $a20 = array('десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать');
        $tens = array(2 => 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто');
        $hundred = array('', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот');
        $unit = array( // Units
            array('копейка', 'копейки', 'копеек', 1),
            array('рубль', 'рубля', 'рублей', 0),
            array('тысяча', 'тысячи', 'тысяч', 1),
            array('миллион', 'миллиона', 'миллионов', 0),
            array('миллиард', 'милиарда', 'миллиардов', 0),
        );

        list($rub, $kop) = explode('.', sprintf('%015.2f', floatval($num)));
        $out = array();

        if (intval($rub) > 0) {
            foreach (str_split($rub, 3) as $uk => $v) { // by 3 symbols
                if (!intval($v)) {
                    continue;
                }
                $uk = sizeof($unit) - $uk - 1; // unit key
                $gender = $unit[$uk][3];
                list($i1, $i2, $i3) = array_map('intval', str_split($v, 1));
                // mega-logic
                $out[] = $hundred[$i1]; // 1xx-9xx
                if ($i2 > 1) {
                    $out[] = $tens[$i2].' '.$ten[$gender][$i3];
                } // 20-99
                else {
                    $out[] = $i2 > 0 ? $a20[$i3] : $ten[$gender][$i3];
                } // 10-19 | 1-9
                // units without rub & kop
                if ($uk > 1) {
                    $out[] = self::morph($v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2]);
                }
            } //foreach
        } else {
            $out[] = $nul;
        }
        $out[] = self::morph(intval($rub), $unit[1][0], $unit[1][1], $unit[1][2]); // rub
        $out[] = $kop.' '.self::morph($kop, $unit[0][0], $unit[0][1], $unit[0][2]); // kop
        return trim(preg_replace('/ {2,}/', ' ', join(' ', $out)));
    }

    public static function num2shortStr($num)
    {
        list($rub, $kop) = explode('.', sprintf('%015.2f', floatval($num)));
        $kop .= ' '.self::morph($kop, 'копейка', 'копейки', 'копеек');

        return self::fShort($rub).' '.$kop;
    }

    /**
     * Склоняем словоформу.
     *
     * @ author runcore
     *
     * @param $n
     * @param $f1
     * @param $f2
     * @param $f5
     *
     * @return
     */
    public static function morph($n, $f1, $f2, $f5)
    {
        $n = abs(intval($n)) % 100;

        if ($n > 10 && $n < 20) {
            return $f5;
        }
        $n = $n % 10;

        if ($n > 1 && $n < 5) {
            return $f2;
        }

        if (1 == $n) {
            return $f1;
        }

        return $f5;
    }

    public static function recalculateAverage($oldCount, $oldAverage, $newCount, $delta)
    {
        return $newCount ? ($oldAverage * $oldCount + $delta) / $newCount : 0;
    }

    public static function points($points, Partner $partner = null, $precision = null): string
    {
        $partner = $partner ?? HContext::getPartner();
        $pointsNum = ((int) $points == $points) ? (int) $points : (float) $points;

        if ($partner && $partner->isBitrewardsEnabled()) {
            $pointsNum = static::floor($pointsNum, $precision);

            return "{$pointsNum} BIT";
        }

        return __('%count% {point|points}', $pointsNum);
    }

    public static function pointsWithSign($points)
    {
        $sign = $points > 0 ? '+' : '';

        return $sign.HAmount::points($points);
    }

    public static function pointsGenitive($points)
    {
        $partner = HContext::getPartner();

        $pointsStr = ((int) $points == $points) ? (int) $points : (float) $points;

        return
            $partner && $partner->isBitrewardsEnabled() ?
                $pointsStr.' BIT' :
                __('%count% {point|points}', ((int) $points == $points) ? (int) $points : $points);
    }

    public static function toLocalCurrency($roubles, $language = null)
    {
        if (null === $language) {
            $language = HLanguage::getCurrent();
        }

        if (HLanguage::LANGUAGE_EN == $language) {
            return static::round($roubles / static::ROUBLES_PER_USD, 2);
        } else {
            return $roubles;
        }
    }

    public static function fiatToPoints($value, Partner $partner, $forceBit = false)
    {
        if (!$partner->isBitrewardsEnabled() && !$forceBit) {
            return $value * $partner->money_to_points_multiplier;
        }

        $currency = static::sISO4217($partner->currency);
        $rate = app(FiatService::class)->getBitToFiatRate($currency);

        if (empty($rate)) {
            throw new \RuntimeException("Rate for currency '{$currency}' not exist");
        }

        return static::round($value / $rate, 18);
    }

    public static function pointsToFiat($value, Partner $partner, $forceBit = false)
    {
        if (!$partner->isBitrewardsEnabled() && !$forceBit) {
            return $value / $partner->money_to_points_multiplier;
        }

        $rate = app(FiatService::class)->getBitToFiatRate(
            static::sISO4217($partner->currency)
        );

        return static::round($value * $rate, 2);
    }

    public static function pointsWithPartner($points, $partner)
    {
        if ($partner) {
            \HContext::setPartner($partner);
            $points = static::points($points);
            \HContext::restorePartner();

            return $points;
        }

        return static::points($points);
    }

    public static function getInParentCurrency($bitAmount, Partner $partner): string
    {
        $partnerCurrency = self::sISO4217($partner->currency);
        $exchangeRate = app(FiatService::class)->getExchangeRate('BIT', $partnerCurrency);
        $amount = self::round($bitAmount * $exchangeRate, 2);

        return self::fSign($amount, $partnerCurrency);
    }

    public static function floor($number, $precision = 2)
    {
        return floor($number * pow(10, $precision)) / pow(10, $precision);
    }

    public static function shorten($number)
    {
        if ($number > self::SHORTEN_THRESHOLD) {
            $number /= self::SHORTEN_THRESHOLD;

            return number_format($number, 1, ',', ' ').' '._('mln');
        }

        return number_format($number, 0, ',', ' ');
    }

    public static function shouldBeShortened($number)
    {
        return $number > self::SHORTEN_THRESHOLD;
    }

    public static function stripCurrencySign(string $amountString): string
    {
        return str_replace([
            self::ROUBLE_REGULAR,
            self::ROUBLE_BOLD,
        ], '₽', $amountString);
    }

    public static function getFiatWithdrawFeeStr(Partner $partner): string
    {
        $feeType = $partner->getFiatWithdrawFeeType();

        switch ($feeType) {
            case Partner::FIAT_WITHDRAW_FEE_TYPE_PERCENT:
                $fee = $partner->getFiatWithdrawFee();

                return $fee ? "{$fee}%" : '0';

            case Partner::FIAT_WITHDRAW_FEE_TYPE_FIXED:
                return self::fMedium($partner->getFiatWithdrawFee(), $partner->currency);

            case null:
                return '0';

            default:
                throw new \DomainException("Fee type '{$feeType}' not implemented'");
        }
    }

    public static function pointsToFiatFormatted($value, Partner $partner, $noWhiteSpace = false): string
    {
        $amount = self::pointsToFiat($value, $partner);

        return self::fMedium($amount, $partner->currency, $noWhiteSpace);
    }

    public static function pointsToPercentFormatted($value, $withSign = false)
    {
        switch ($value) {
            case 0:
                $result = 0;

                break;

            case 100:
                $result = 25;

                break;

            case 200:
                $result = 50;

                break;

            case 300:
                $result = 75;

                break;

            default:
                $result = 100;

                break;
        }

        if ($withSign) {
            $result = "+{$result}";
        }

        return $result.'%';
    }
}
