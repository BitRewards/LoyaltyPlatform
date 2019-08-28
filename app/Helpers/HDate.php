<?php

use Carbon\Carbon;

class HDate
{
    const PERIOD_DAY = 'day';
    const PERIOD_NIGHT = 'night';
    const PERIOD_EVENING = 'evening';
    const PERIOD_MORNING = 'morning';

    private static $_DAYS = null;

    const FORMAT_DATE_FULL = 'date_full';
    const FORMAT_DATE_FULL_YEAR_4_DIGIT = 'date_full_year_4digit';
    const FORMAT_DATE_SHORT = 'date_short';
    const FORMAT_TIME_WITH_SECONDS = 'time_with_seconds';
    const FORMAT_TIME = 'time';

    public const TIMEZONE_UTC = 'UTC';
    public const TIMEZONE_MOSCOW = 'MSK';

    private static $formats = [
        HLanguage::LANGUAGE_EN => [
            self::FORMAT_DATE_FULL => 'm/d/y',
            self::FORMAT_DATE_FULL_YEAR_4_DIGIT => 'm/d/Y',
            self::FORMAT_DATE_SHORT => 'm/d',
            self::FORMAT_TIME_WITH_SECONDS => 'g:i:s a',
            self::FORMAT_TIME => 'g:i a',
        ],
        HLanguage::LANGUAGE_CN => [
            self::FORMAT_DATE_FULL => 'm/d/y',
            self::FORMAT_DATE_FULL_YEAR_4_DIGIT => 'm/d/Y',
            self::FORMAT_DATE_SHORT => 'm/d',
            self::FORMAT_TIME_WITH_SECONDS => 'g:i:s a',
            self::FORMAT_TIME => 'g:i a',
        ],
        HLanguage::LANGUAGE_TR => [
            self::FORMAT_DATE_FULL => 'd.m.y',
            self::FORMAT_DATE_FULL_YEAR_4_DIGIT => 'd.m.Y',
            self::FORMAT_DATE_SHORT => 'd.m',
            self::FORMAT_TIME_WITH_SECONDS => 'H:i:s',
            self::FORMAT_TIME => 'H:i',
        ],
        HLanguage::LANGUAGE_RU => [
            self::FORMAT_DATE_FULL => 'd.m.y',
            self::FORMAT_DATE_FULL_YEAR_4_DIGIT => 'd.m.Y',
            self::FORMAT_DATE_SHORT => 'd.m',
            self::FORMAT_TIME_WITH_SECONDS => 'H:i:s',
            self::FORMAT_TIME => 'H:i',
        ],
    ];

    public static function getCurrentUserUtcDiff()
    {
        return HLanguage::isRussian() ? HDate::getMoscowUtcOffset() : 0;
    }

    public static function getCurrentUserTimeZoneName(): string
    {
        return HLanguage::isRussian() ? self::TIMEZONE_MOSCOW : self::TIMEZONE_UTC;
    }

    private static function getFormat($name)
    {
        return static::$formats[HLanguage::getCurrent()][$name];
    }

    public static function date($timestamp, $addTimezone = true)
    {
        if ($timestamp instanceof Carbon) {
            $timestamp = $timestamp->timestamp;
        }

        $utcDiff = self::getCurrentUserUtcDiff();

        if ($addTimezone) {
            $time = $timestamp + $utcDiff;
        } else {
            $time = $timestamp;
        }

        $currentTime = time() + $utcDiff;

        if (date('y', $currentTime) != date('y', $time)) {
            $date = date(self::getFormat(self::FORMAT_DATE_FULL), $time);
        } elseif (date('m.d', $currentTime) == date('m.d', $time)) {
            $date = _('today');
        } else {
            $date = date(self::getFormat(self::FORMAT_DATE_SHORT), $time);
        }

        return $date;
    }

    public static function adjust($timestamp)
    {
        return $timestamp + self::getCurrentUserUtcDiff();
    }

    public static function time($timestamp, $withSeconds = false)
    {
        if ($timestamp instanceof Carbon) {
            $timestamp = $timestamp->timestamp;
        }

        $timestamp += self::getCurrentUserUtcDiff();

        return date($withSeconds ? self::getFormat(self::FORMAT_TIME_WITH_SECONDS) : self::getFormat(self::FORMAT_TIME), $timestamp);
    }

    public static function dateTime($timestamp, $withSeconds = false)
    {
        if ($timestamp instanceof Carbon) {
            $timestamp = $timestamp->getTimestamp();
        }

        $timestamp += self::getCurrentUserUtcDiff();

        return self::date($timestamp, false).', '.date($withSeconds ? self::getFormat(self::FORMAT_TIME_WITH_SECONDS) : self::getFormat(self::FORMAT_TIME), $timestamp);
    }

    public static function dateFull($timestamp)
    {
        if (!$timestamp) {
            return '';
        }

        if ($timestamp instanceof Carbon) {
            $timestamp = $timestamp->getTimestamp();
        }

        $timestamp += self::getCurrentUserUtcDiff();

        return date(self::getFormat(self::FORMAT_DATE_FULL), $timestamp);
    }

    public static function dateStrFull($timestamp, $informal = false, $yearRequired = false)
    {
        if (!$timestamp) {
            return '';
        }

        $now = time();

        $timestamp += self::getCurrentUserUtcDiff();

        $year = date('Y', $timestamp);
        $diff = abs($timestamp - $now);

        if ($informal && date('W', $timestamp) == date('W', $now) && HLanguage::isRussian()) {
            if (!self::$_DAYS) {
                self::$_DAYS = [__('on Sunday'), __('on Monday'), __('on Tuesday'), __('on Wednesday'), __('on Thursday'), __('on Friday'), __('on Saturday')];
            }

            return self::$_DAYS[date('w', $timestamp)];
        }

        $includeYear = ($year != date('Y') && $diff > 360 * 24 * 3600 || $yearRequired);

        if (HLanguage::isEnglish()) {
            return date($includeYear ? 'F j, Y' : 'F j', $timestamp);
        } else {
            return
                date('j', $timestamp).' '.
                self::monthGenitive(date('m', $timestamp)).
                ($includeYear ? (' '.$year) : '');
        }
    }

    public static function dateTimeStrFuture($timestamp)
    {
        if ($timestamp instanceof Carbon) {
            $timestamp = $timestamp->getTimestamp();
        }

        if (time() >= $timestamp) {
            return _('right now');
        }

        return self::dateStrFull($timestamp, true).' '.__('at').' '.self::time($timestamp);
    }

    public static function dateStrFuture($timestamp)
    {
        return self::dateStrFull($timestamp, true);
    }

    public static function dateTimeStrFull($timestamp, $separator = ', ')
    {
        return self::dateStrFull($timestamp).$separator.self::time($timestamp);
    }

    public static function dateTimeFull($timestamp, $withSeconds = false, $dateTimeSeparator = ', ')
    {
        if (!$timestamp) {
            return '';
        }

        if ($timestamp instanceof Carbon) {
            $timestamp = $timestamp->getTimestamp();
        }

        $time = self::time($timestamp, $withSeconds);
        $date = self::dateFull($timestamp);

        return $date.$dateTimeSeparator.$time;
    }

    public static function toDays($seconds)
    {
        return $seconds ? floor($seconds / (24 * 3600)) : '';
    }

    public static function toMinutes($seconds)
    {
        return $seconds ? ceil($seconds / (60)) : '';
    }

    public static function toHours($seconds)
    {
        return $seconds ? floor($seconds / (3600)) : '';
    }

    public static function daysToSeconds($days)
    {
        return $days * 24 * 3600;
    }

    public static function parseDate($date, $parseTime = false, $timeWithSeconds = true)
    {
        if (strlen($date) < 6) {
            return false;
        }
        $timeFormat = $parseTime ? ($timeWithSeconds ? ' H:i:s' : ' H:i') : '';

        if ('-' == $date[4]) {
            $format = 'Y-m-d'.$timeFormat;
        } else {
            $yearDotPosition = (strpos($date, ' ') ?: strlen($date)) - 3;

            switch ($date[$yearDotPosition]) {
                case '.':
                    $format = 'd.m.y';

                    break;

                case '/':
                    $format = 'm/d/y';

                    break;

                default:
                    $format = strpos($date, '/') ? 'm/d/Y' : 'd.m.Y';

                    break;
            }
            $format .= $timeFormat;
        }

        date_default_timezone_set('UTC');
        $parsed = @date_parse_from_format($format, $date);

        if (!$parsed) {
            return false;
        }

        if (isset($parsed['hour']) && !is_int($parsed['hour'])) {
            unset($parsed['hour']);
        }

        if (isset($parsed['minute']) && !is_int($parsed['minute'])) {
            unset($parsed['minute']);
        }

        if (isset($parsed['second']) && !is_int($parsed['second'])) {
            unset($parsed['second']);
        }

        return $parsed;
    }

    public static function dateToTimestamp($date, $hours = 0, $minutes = 0, $seconds = 0, $moscowTime = false)
    {
        if (!$date) {
            return false;
        }
        $parsed = HDate::parseDate($date);

        if (!$parsed) {
            return false;
        }

        return mktime(
            $hours,
            $minutes,
            $seconds,
            $parsed['month'],
            $parsed['day'],
            $parsed['year']
        ) - ($moscowTime ? 0 : HDate::getCurrentUserUtcDiff());
    }

    public static function filterDateToTimestamp($date, $hours = 0, $minutes = 0, $seconds = 0, $moscowTime = false)
    {
        if (!$date) {
            return false;
        }

        $filter_length = 0;

        for ($i = 0; $i < strlen($date); ++$i) {
            if (!ctype_digit($date[$i])) {
                ++$filter_length;
            } else {
                break;
            }
        }
        $filter = substr($date, 0, $filter_length);
        $data = substr($date, $filter_length);

        return $filter.static::dateToTimestamp($date, $hours, $minutes, $seconds, $moscowTime);
    }

    public static function isFilter($date)
    {
        return strlen($date) && !ctype_digit($date[0]);
    }

    public static function getMonthNumber($timestamp)
    {
        return (int) date('m', $timestamp + self::getCurrentUserUtcDiff());
    }

    public static function getMoscowUtcOffset()
    {
        $date = new DateTime('now', new DateTimeZone('Europe/Moscow'));

        return $date->getOffset();
    }

    public static function getSanFranciscoUtcOffset()
    {
        $date = new DateTime('now', new DateTimeZone('America/Los_Angeles'));

        return $date->getOffset();
    }

    public static function getMoscowUtcOffsetHours()
    {
        return static::getMoscowUtcOffset() / 3600;
    }

    public static function getCurrentMoscowTime()
    {
        return time() + self::getMoscowUtcOffset();
    }

    public static function getDayPeriod($time)
    {
        $hour = (int) date('H', $time);

        if ($hour >= 0 && $hour <= 3) {
            return self::PERIOD_NIGHT;
        }

        if ($hour >= 4 && $hour <= 10) {
            return self::PERIOD_MORNING;
        }

        if ($hour >= 17) {
            return self::PERIOD_EVENING;
        }

        return self::PERIOD_DAY;
    }

    public static function interval($seconds, $includeNumber = true)
    {
        if ($seconds >= 86400) {
            return self::daysInterval($seconds, $includeNumber);
        } elseif ($seconds >= 3600) {
            return self::hoursInterval($seconds, $includeNumber);
        } else {
            return self::minutesInterval($seconds, $includeNumber);
        }
    }

    public static function intervalGenitive($seconds, $includeNumber = true)
    {
        if ($seconds >= 86400) {
            return self::daysIntervalGenitive($seconds, $includeNumber);
        } elseif ($seconds >= 3600) {
            return self::hoursIntervalGenitive($seconds, $includeNumber);
        } else {
            return self::minutesIntervalGenitive($seconds, $includeNumber);
        }
    }

    public static function daysInterval($seconds, $includeNumber = true)
    {
        $days = self::toDays($seconds);

        return HLanguage::countForm($days, _('day'), _('days'), _('days'), $includeNumber);
    }

    public static function minutesInterval($seconds, $includeNumber = true)
    {
        $minutes = self::toMinutes($seconds);

        return HLanguage::countForm($minutes, _('minute'), _('minutes'), _('minutes'), $includeNumber);
    }

    public static function hoursInterval($seconds, $includeNumber = true)
    {
        $hours = self::toHours($seconds);

        return HLanguage::countForm($hours, _('hour'), _('hours'), _('hours'), $includeNumber);
    }

    public static function minutesIntervalGenitive($seconds, $includeNumber = true)
    {
        $hours = self::toMinutes($seconds);

        return HLanguage::countForm($hours, _('minutes'), _('minutes'), _('minutes'), $includeNumber);
    }

    public static function hoursIntervalGenitive($seconds, $includeNumber = true)
    {
        $hours = self::toHours($seconds);

        return HLanguage::countForm($hours, _('hours'), _('hours'), _('hours'), $includeNumber);
    }

    public static function daysIntervalGenitive($seconds, $includeNumber = true)
    {
        $days = self::toDays($seconds);

        return HLanguage::countForm($days, _('days'), _('days'), _('days'), $includeNumber);
    }

    public static function roundToMoscowBeginningOfDay($time)
    {
        return static::roundToBeginningOfDay($time, static::getMoscowUtcOffset());
    }

    public static function roundToMoscowEndOfDay($time)
    {
        return static::roundToEndOfDay($time, static::getMoscowUtcOffset());
    }

    public static function roundToUserBeginningOfDay($time)
    {
        return static::roundToBeginningOfDay($time, static::getCurrentUserUtcDiff());
    }

    public static function roundToUserEndOfDay($time)
    {
        return static::roundToEndOfDay($time, static::getCurrentUserUtcDiff());
    }

    public static function roundToBeginningOfDay($time, $offset)
    {
        $result = mktime(0, 0, 0, date('m', $time), date('d', $time), date('Y', $time));

        return $result - $offset;
    }

    public static function roundToEndOfDay($time, $offset)
    {
        $result = mktime(23, 59, 59, date('m', $time), date('d', $time), date('Y', $time));

        return $result - $offset;
    }

    public static function monthPrepositional($monthNumber)
    {
        $monthNumber = (int) $monthNumber;

        switch ($monthNumber) {
            case 1:
                return _('In January');

            case 2:
                return _('in February');

            case 3:
                return _('in March');

            case 4:
                return _('In April');

            case 5:
                return _('In May');

            case 6:
                return _('In June');

            case 7:
                return _('In July');

            case 8:
                return _('In August');

            case 9:
                return _('In September');

            case 10:
                return _('In October');

            case 11:
                return _('In November');

            case 12:
                return _('In December');

            default:
                return '—';
        }
    }

    public static function monthGenitive($monthNumber)
    {
        $monthNumber = (int) $monthNumber;

        switch ($monthNumber) {
            case 1:
                return __('January');

            case 2:
                return __('February');

            case 3:
                return __('March');

            case 4:
                return __('April');

            case 5:
                return __('May');

            case 6:
                return __('June');

            case 7:
                return __('July');

            case 8:
                return __('August');

            case 9:
                return __('September');

            case 10:
                return __('October');

            case 11:
                return __('November');

            case 12:
                return __('December');

            default:
                return __('Martober');
        }
    }

    public static function monthCommon($monthNumber)
    {
        $monthNumber = (int) $monthNumber;

        switch ($monthNumber) {
            case 1:
                return __('January');

            case 2:
                return __('February');

            case 3:
                return __('March');

            case 4:
                return __('April');

            case 5:
                return __('May');

            case 6:
                return __('June');

            case 7:
                return __('July');

            case 8:
                return __('August');

            case 9:
                return __('September');

            case 10:
                return __('October');

            case 11:
                return __('November');

            case 12:
                return __('December');

            default:
                return __('Martober');
        }
    }

    public static function isEgorovBirthday()
    {
        return '08.07' == date('d.m', time() + HDate::getMoscowUtcOffset());
    }

    public static function now()
    {
        return HDate::dateTimeFull(time(), true);
    }

    public static function insertMissingPoints($timestampsRange, &$data, $zeroRow, $getTimestampCb, $setTimestampCb)
    {
        $exist = [];

        foreach ($data as $row) {
            $exist[$getTimestampCb($row)] = true;
        }

        foreach ($timestampsRange as $timestamp) {
            if (!isset($exist[$timestamp])) {
                $row = $zeroRow;
                $key = $setTimestampCb($row, $timestamp);

                if ($key) {
                    $data[$key] = $row;
                } else {
                    $data[] = $row;
                }
            }
        }

        usort($data, function ($a, $b) use ($getTimestampCb, $setTimestampCb) {
            return $getTimestampCb($a) - $getTimestampCb($b);
        });
    }

    public static function expiringDateFormat(?Carbon $date): ?string
    {
        if (!$date) {
            return null;
        }

        $binds = [
            'date' => self::date($date),
            'time' => self::time($date),
        ];

        $daysLeft = $date->diffInDays() * ($date->isPast() ? -1 : 1);

        if (-1 > $daysLeft) {
            return __('Expired %time%, %date%', $binds);
        }

        if (-1 === $daysLeft) {
            return __('Expired yesterday at %time%', $binds);
        }

        if (0 === $daysLeft) {
            if ($date->isPast()) {
                return __('Expired today at %time%', $binds);
            }

            return __('Expires today at %time%', $binds);
        }

        if (1 === $daysLeft) {
            return __('Expires tomorrow at %time%', $binds);
        }

        return __('Expires in %days% days', $daysLeft);
    }
}
