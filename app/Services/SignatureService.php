<?php

namespace App\Services;

class SignatureService
{
    const STATIC_SIGNATURE_SALT = 'wXsdf2163fij1SF6wef';

    public function sign($data)
    {
        $data = $this->toString($data).self::STATIC_SIGNATURE_SALT;

        return \Hash::make($data);
    }

    public function check($data, $signature)
    {
        $data = $this->toString($data).self::STATIC_SIGNATURE_SALT;

        return \Hash::check($data, $signature);
    }

    private function toString($data)
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        if (is_array($data)) {
            $data = implode('', $data);
        }

        return (string) $data;
    }
}
