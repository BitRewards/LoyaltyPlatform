<?php

namespace App\Rabbit\Validator;

use Illuminate\Validation\Factory;
use Illuminate\Validation\Validator;

/**
 * @method Validator validate($object)
 */
abstract class AbstractValidator
{
    /**
     * @var Factory
     */
    private $validationFactory;

    final public function __construct(Factory $validationFactory)
    {
        $this->validationFactory = $validationFactory;
    }

    abstract protected function getValidatorExceptionClass(): string;

    abstract protected function getRules(): array;

    abstract protected function getErrorCodes(): array;

    protected function getCustomAttributes(): array
    {
        return [];
    }

    public function validateData(array $data): Validator
    {
        return $this
            ->validationFactory
            ->make($data, $this->getRules(), $this->getErrorCodes(), $this->getCustomAttributes());
    }

    public function __call($name, $arguments)
    {
        if ('validate' === $name) {
            throw new \RuntimeException('validate method not implemented');
        }
    }

    public function validateOrFail($object): void
    {
        $validator = $this->validate($object);

        if ($validator->fails()) {
            $exceptionClass = $this->getValidatorExceptionClass();

            throw new $exceptionClass($validator->messages()->first());
        }
    }
}
