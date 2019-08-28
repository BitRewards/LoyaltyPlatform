<?php

namespace App\Services\Api\Specification;

use LogicException;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Api\Specification\InvalidSchemaPayload;

trait ValidatorTrait
{
    /**
     * Schema validator instance.
     *
     * @var \Illuminate\Validation\Validator
     */
    protected $validator;

    /**
     * Validation rules.
     *
     * @return array
     */
    abstract public function rules();

    /**
     * Validate current schema payload.
     *
     * @return bool
     */
    public function validate(array $payload, bool $exceptionOnFailure = true): bool
    {
        $this->validator = Validator::make($payload, $this->rules());

        if ($this->validator->passes()) {
            return true;
        } elseif (false === $exceptionOnFailure) {
            return false;
        }

        throw new InvalidSchemaPayload('Payload validation failed: "'.$this->firstValidationError().'"');
    }

    /**
     * Get first validation error.
     *
     * @throws \LogicException
     *
     * @return \Illuminate\Validation\Validator|null
     */
    public function firstValidationError()
    {
        if (is_null($this->validator)) {
            throw new LogicException('Validator is not ready yet.');
        }

        return $this->validator->errors()->first();
    }
}
