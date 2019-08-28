<?php

namespace App\Http\Requests;

use App\Models\Partner;
use Illuminate\Foundation\Http\FormRequest;
/*
 * @property Partner $partner
 * @property string $email
 * @property string $name
 */
use Illuminate\Contracts\Support\MessageBag;

class BaseFormRequest extends FormRequest
{
    protected $extraValidationCallbacks = [];

    public function validateResolved()
    {
        $this->prepareForValidation();

        if (!$this->passesAuthorization()) {
            $this->failedAuthorization();

            return;
        }

        $instance = $this->getValidatorInstance();
        $instance->after(function () use ($instance) {
            if ($instance->getMessageBag()->isEmpty()) {
                foreach ($this->extraValidationCallbacks as $callback) {
                    call_user_func($callback, $instance->getMessageBag());
                }

                if ($instance->getMessageBag()->isEmpty()) {
                    $this->callExtraValidations($instance->getMessageBag());

                    $this->doExtraValidation($instance->getMessageBag());
                }
            }
        });

        if ($instance->fails()) {
            $this->failedValidation($instance);
        }
    }

    private function callExtraValidations(MessageBag $messageBag)
    {
        $methods = get_class_methods($this);
        $existingMethods = ['validateResolved', 'validated'];

        foreach ($methods as $method) {
            if (in_array($method, $existingMethods)) {
                continue;
            }

            if (preg_match('/^validate[a-zA-Z0-9]+/', $method)) {
                $this->$method($messageBag);
            }
        }
    }

    public function doExtraValidation(MessageBag $messageBag)
    {
    }

    public function addExtraValidationCallback(callable $callback)
    {
        $this->extraValidationCallbacks[] = $callback;
    }
}
