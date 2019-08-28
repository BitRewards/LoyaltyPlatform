<?php

use Illuminate\Validation\ValidationException;

class HValidator
{
    public static function createValidationException($message, $field = null)
    {
        if (!$field) {
            $field = '__field__';
        }
        $validator = \Validator::make([$field => ''], []);
        $validator->getMessageBag()->add($field, $message);

        return new ValidationException($validator);
    }
}
