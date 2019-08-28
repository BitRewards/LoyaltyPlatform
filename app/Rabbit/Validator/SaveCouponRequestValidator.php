<?php

namespace App\Rabbit\Validator;

use GL\Rabbit\DTO\RPC\CRM\SaveCouponRequest;
use GL\Rabbit\Validator\Exception\SaveCouponRequestValidatorException as ValidatorException;
use Illuminate\Validation\Validator;

class SaveCouponRequestValidator extends AbstractValidator
{
    protected function getValidatorExceptionClass(): string
    {
        return ValidatorException::class;
    }

    protected function getRules(): array
    {
        return [
            'giftdPartnerId' => 'exists:partners,giftd_id',
            'code' => 'required|string',
            'email' => 'nullable|string|email|required_without:phone',
            'phone' => 'nullable|string|required_without:email',
            'discountAmount' => 'nullable|numeric',
            'discountPercent' => 'nullable|numeric',
            'discountStr' => 'nullable|string',
            'minAmountTotal' => 'nullable|numeric',
            'expires' => 'nullable|integer',
            'referrerKey' => 'nullable',
        ];
    }

    protected function getErrorCodes(): array
    {
        return [
            'giftdPartnerId.*' => ValidatorException::PARTNER_NOT_EXIST,
            'code.*' => ValidatorException::COUPON_CODE_REQUIRED,
            'email.required_without' => ValidatorException::EMAIL_OR_PHONE_REQUIRED,
            'phone.required_without' => ValidatorException::EMAIL_OR_PHONE_REQUIRED,
            'email.*' => ValidatorException::INVALID_EMAIL,
            'phone.*' => ValidatorException::INVALID_PHONE_NUMBER,
            'discountAmount.*' => ValidatorException::INVALID_DISCOUNT_AMOUNT,
            'discountPercent.*' => ValidatorException::INVALID_DISCOUNT_PERCENT,
            'discountStr.*' => ValidatorException::INVALID_DISCOUNT_DESCRIPTION,
            'minAmountTotal.*' => ValidatorException::INVALID_MINIMAL_AMOUNT,
            'expires.*' => ValidatorException::INVALID_EXPIRES_VALUE,
        ];
    }

    public function validate(SaveCouponRequest $request): Validator
    {
        return $this->validateData((array) $request);
    }
}
