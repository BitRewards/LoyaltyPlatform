<?php

namespace App\Rabbit\Validator;

use GL\Rabbit\DTO\RPC\CRM\PartnerStatisticRequest;
use GL\Rabbit\Validator\Exception\PartnerStatisticRequestValidatorException as ValidatorException;
use Illuminate\Validation\Validator;

class PartnerStatisticRequestValidator extends AbstractValidator
{
    protected function getValidatorExceptionClass(): string
    {
        return ValidatorException::class;
    }

    protected function getRules(): array
    {
        return [
            'giftdPartnerId' => 'exists:partners,giftd_id',
        ];
    }

    protected function getErrorCodes(): array
    {
        return [
            'giftdPartnerId.*' => ValidatorException::PARTNER_NOT_EXIST,
        ];
    }

    public function validate(PartnerStatisticRequest $request): Validator
    {
        return $this->validateData([
            'giftdPartnerId' => $request->getGiftdPartnerId(),
        ]);
    }
}
