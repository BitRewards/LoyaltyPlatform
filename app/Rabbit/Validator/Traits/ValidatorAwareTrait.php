<?php

namespace App\Rabbit\Validator\Traits;

use App\Rabbit\Validator\AbstractValidator;

trait ValidatorAwareTrait
{
    /**
     * @var AbstractValidator
     */
    protected $validator;

    protected function setValidator(AbstractValidator $validator): void
    {
        $this->validator = $validator;
    }

    protected function getValidator(): AbstractValidator
    {
        return $this->validator;
    }

    protected function validateOrFail($object): void
    {
        $validator = $this->getValidator()->validate($object);

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->messages()->first());
        }
    }
}
