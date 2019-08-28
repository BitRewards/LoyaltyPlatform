<?php

use App\Models\User;

class HUser
{
    public static function getPictureOrPlaceholder(User $user)
    {
        if ($user->picture) {
            return $user->picture;
        } else {
            $firstLetterSource = trim(($user->name ?: $user->email) ?: $user->phone, '+');

            if (!$firstLetterSource) {
                $firstLetterSource = ' ';
            }

            $firstLetter = HStr::getFirstLetterUrlencoded($firstLetterSource);

            return "https://ui-avatars.com/api/?name=$firstLetter&background=00a65a&color=fff&size=60";
        }
    }

    public static function getPersonalData(User $user, $mask = false)
    {
        $userData = [];

        if ($user->vk_id) {
            $userData[__('VK ID')] = $user->vk_id;
        }

        if ($user->fb_id) {
            $userData[__('Facebook ID')] = $user->fb_id;
        }

        if ($user->email) {
            $userData[__('Email')] = $user->email;
        }

        if ($user->phone) {
            $userData[__('Phone')] = $user->phone;
        }

        if ($user->name) {
            $userData[__('Name')] = $user->name;
        }

        if ($mask) {
            foreach ($userData as $key => &$value) {
                $value = substr($value, 3).'***'.substr($value, -3);
            }
        }

        return $userData;
    }

    /**
     * @param $phone
     * @param null $defaultCountry
     * @param bool $strict
     *
     * @return string|string[]|null
     */
    public static function normalizePhone(?string $phone, string $defaultCountry, $strict = false)
    {
        if (!trim($phone)) {
            return null;
        }

        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();

        $defaultCountry = trim(mb_strtoupper($defaultCountry));

        if (!$defaultCountry || 2 != strlen($defaultCountry)) {
            throw new RuntimeException('defaultCountry parameter is mandatory and should have a length of 2 latin chars');
        }

        if (preg_match("/^\+89\d{9}$/", $phone) && 'RU' == $defaultCountry) { // malformed Russian phone number like +89110366691 instead of +79110366691
            $phone = str_replace('+89', '+79', $phone);
        }

        try {
            $phoneNumber = $phoneUtil->parse($phone, $defaultCountry);

            if ($phoneUtil->isValidNumber($phoneNumber)) {
                return trim($phoneUtil->format($phoneNumber, \libphonenumber\PhoneNumberFormat::E164));
            }
        } catch (\libphonenumber\NumberParseException $e) {
            \Log::debug('normalizePhone failed', [
                'error' => $e->getMessage(),
                'phone' => $phone,
                'defaultCountry' => $defaultCountry,
            ]);
        }

        if ($strict) {
            return null;
        } else {
            $phone = preg_replace("/[^\+\d]+/", '', $phone);
            // in case we failed just return the original with all non-digits and + stripped out
            return $phone;
        }
    }

    public static function normalizeEmail($email)
    {
        if (null === $email) {
            return null;
        }

        return mb_substr(mb_strtolower(trim($email)), 0, 255);
    }

    public static function getFingerprints()
    {
        return [
            'user' => \Auth::user(),
            'ip' => \Request::ip(),
            'cookies' => \Cookie::get(),
            'timestamp' => time(),
            'userAgent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        ];
    }

    public static function getFirstName($fullName)
    {
        $parts = preg_split("/\s+/", trim($fullName));

        return $parts[0];
    }
}
