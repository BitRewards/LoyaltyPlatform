<?php

/**
 * HHttp helper class.
 *
 * Utility class to work with HTTP erroor classes.
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 *
 * @see http://www.ramirezcobos.com/
 * @see http://www.2amigos.us/
 *
 * @copyright 2013 2amigOS! Consultation Group LLC
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class HStr
{
    public static function mask($token)
    {
        if (!$token) {
            return '';
        }
        $length = strlen($token);
        $keepFromEnd = $length <= 8 ? 3 : ($length >= 10 ? 4 : 3);
        $stars = '***************************************';

        return "<small class='stars'>".substr($stars, 0, strlen($token) - $keepFromEnd).'</small>'.substr($token, -$keepFromEnd);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public static function splitByNewLine($data)
    {
        return array_values(array_map('trim', preg_split('/(\n|\r)+/', $data, -1, PREG_SPLIT_NO_EMPTY)));
    }

    /**
     * @param $line
     *
     * @return array
     */
    public static function splitByTab($line)
    {
        return array_values(array_filter(array_map('trim', explode("\t", $line)), 'strlen'));
    }

    /**
     * @param $str
     *
     * @return int
     */
    public static function isPhone($str)
    {
        return preg_match('/^[\+\(\)\-0-9]{9,20}$/', $str);
    }

    public static function normalizeEthNumber(string $ethNumber): string
    {
        return mb_strtolower(trim($ethNumber));
    }

    protected static function lastDigitsFromCard(?string $cardNumber): ?string
    {
        if (null === $cardNumber || trim($cardNumber) < 4) {
            return null;
        }

        return substr(trim($cardNumber), -4);
    }

    public static function cardMask(?string $cardNumber): ?string
    {
        $lastDigits = self::lastDigitsFromCard($cardNumber);

        return $lastDigits ? "**** **** **** {$lastDigits}" : null;
    }

    public static function shortCardMask(?string $cardNumber): ?string
    {
        $lastDigits = self::lastDigitsFromCard($cardNumber);

        return $lastDigits ? "**** {$lastDigits}" : null;
    }

    public static function getFirstLetterUrlencoded($firstLetterSource)
    {
        $firstLetter = mb_substr($firstLetterSource, 0, 1, 'utf-8');

        return urlencode($firstLetter);
    }

    public static function convertToBulkRowFormat($data)
    {
        return array_values(array_map(function ($a) {
            return implode("\t", $a);
        }, $data));
    }
}
