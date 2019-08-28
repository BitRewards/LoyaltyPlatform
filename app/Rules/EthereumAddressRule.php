<?php

namespace App\Rules;

class EthereumAddressRule
{
    public function validate($attribute, $value, $parameters, $validator)
    {
        if (empty($value) || !preg_match('/^(0x)[0-9a-f]{40}$/i', $value)) {
            return false;
        }

        return true;
    }
}
