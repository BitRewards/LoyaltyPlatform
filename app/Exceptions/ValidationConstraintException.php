<?php

namespace App\Exceptions;

class ValidationConstraintException extends \InvalidArgumentException
{
    /**
     * @var array
     */
    private $constraint;

    public function __construct($field, $message, int $code = null)
    {
        parent::__construct($message);

        $this->constraint = [
            'field' => $field,
            'message' => $message,
        ];

        if (null !== $code) {
            $this->constraint = [
                'code' => $code,
            ] + $this->constraint;
        }
    }

    public function getConstraint(): array
    {
        return $this->constraint;
    }
}
