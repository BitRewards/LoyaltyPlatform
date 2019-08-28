<?php

class HLanguage
{
    const LANGUAGE_RU = 'ru';
    const LANGUAGE_EN = 'en';
    const LANGUAGE_TR = 'tr';
    const LANGUAGE_CN = 'cn';

    public static function getLanguagesList()
    {
        return [
            self::LANGUAGE_RU => 'Русский',
            self::LANGUAGE_EN => 'English',
            self::LANGUAGE_CN => 'Chineese',
            self::LANGUAGE_TR => 'Turkish',
        ];
    }

    public static function countForm($number, $oneForm, $twoForm, $manyForm, $includeNumber = true)
    {
        $number = (int) $number;
        $smallPart = $number % 100;

        if ($smallPart >= 5 && $smallPart <= 20) {
            return $includeNumber ? ($number.' '.$manyForm) : $manyForm;
        } else {
            $lastDigit = $number % 10;

            switch ($lastDigit) {
                case 1:
                    return $includeNumber ? ($number.' '.$oneForm) : $oneForm;

                case 2:
                case 3:
                case 4:
                    return $includeNumber ? ($number.' '.$twoForm) : $twoForm;

                default:
                    return $includeNumber ? ($number.' '.$manyForm) : $manyForm;
            }
        }
    }

    public static function transliterate($string)
    {
        $converter = [
            'а' => 'a', 'б' => 'b', 'в' => 'v',
            'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
            'и' => 'i', 'й' => 'y', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
            'ь' => '\'', 'ы' => 'y', 'ъ' => '\'',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
            'А' => 'A', 'Б' => 'B', 'В' => 'V',
            'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
            'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
            'И' => 'I', 'Й' => 'Y', 'К' => 'K',
            'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R',
            'С' => 'S', 'Т' => 'T', 'У' => 'U',
            'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
            'Ь' => '\'', 'Ы' => 'Y', 'Ъ' => '\'',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
            '«' => '"', '»' => '"', '„' => '"', '”' => '"',
            '—' => '-', '–' => '-', '№' => '#',
        ];

        return strtr($string, $converter);
    }

    public static function slug($string)
    {
        return trim(preg_replace('/[^a-z0-9_]+/', '-', strtolower(trim(self::transliterate($string)))), '-');
    }

    private static $previousLanguages = [];
    private static $currentLanguage = self::LANGUAGE_EN;

    public static function getCurrent()
    {
        return static::$currentLanguage;
    }

    public static function isRussian()
    {
        return static::getCurrent() == static::LANGUAGE_RU;
    }

    public static function isEnglish()
    {
        return static::getCurrent() == static::LANGUAGE_EN;
    }

    public static function setLanguage($language, $domain = 'default')
    {
        $currentLanguage = static::getCurrent();

        if ($currentLanguage) {
            static::$previousLanguages[] = $currentLanguage;
        }

        if (!$language) {
            $language = HLanguage::LANGUAGE_EN;
        }

        \App::setLocale($language);

        if ($language === $currentLanguage) {
            return;
        }

        static::$currentLanguage = $language;

        $defaultLocale = 'en_US.utf8';

        $localeVariants = self::getLocaleVariants($language);
        $locale = $localeVariants[0];

        putenv('LANGUAGE=');
        putenv('LANGUAGE=');

        putenv('LANG='.$locale);

        putenv('LC_ALL='.$locale);

        setlocale(LC_ALL, $localeVariants[0], $localeVariants[1], $localeVariants[2], $localeVariants[3], $localeVariants[4]);

        putenv('LC_NUMERIC='.$defaultLocale);
        setlocale(LC_NUMERIC, $defaultLocale);

        putenv('LC_MONETARY='.$defaultLocale);
        setlocale(LC_MONETARY, $defaultLocale);
        $path = base_path('resources/lang/i18n');

        bindtextdomain($domain, $path);
        textdomain($domain);

        bind_textdomain_codeset($domain, 'UTF-8');
    }

    private static function getLocaleVariants($locale): array
    {
        switch ($locale) {
            case self::LANGUAGE_RU:
                return ['ru_RU.utf8', 'ru_RU', 'ru', 'RU', 'ru_RU'];

            case self::LANGUAGE_CN:
                return ['zh_CN.utf8', 'cn', 'zh_CN', 'zh_CN', 'zh_CN'];

            case self::LANGUAGE_TR:
                return ['tr_TR.utf8', 'tr', 'tr_TR', 'tr_TR', 'tr_TR'];

            case self::LANGUAGE_EN:
            default:
                return ['en_US.utf8', 'en', 'en_EN', 'en_GB', 'en_US'];
        }
    }

    public static function restorePreviousLanguage()
    {
        if ($language = array_pop(static::$previousLanguages)) {
            static::setLanguage($language);
        }
    }

    public static function getDefaultLocaleForLanguage($language)
    {
        switch ($language) {
            case self::LANGUAGE_RU:
                return 'ru_RU';

            case self::LANGUAGE_CN:
                return 'zh_CN';

            case self::LANGUAGE_TR:
                return 'tr_TR';

            case self::LANGUAGE_EN:
            default:
                return 'en_US';
        }
    }

    public static function getDefaultCountryForLanguage($language)
    {
        switch ($language) {
            case self::LANGUAGE_RU:
                return 'ru';

            case self::LANGUAGE_CN:
                return 'cn';

            case self::LANGUAGE_TR:
                return 'tr';

            case self::LANGUAGE_EN:
                return 'us';

            default:
                return null;
        }
    }
}
