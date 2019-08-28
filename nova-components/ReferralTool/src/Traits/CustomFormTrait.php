<?php

namespace Bitrewards\ReferralTool\Traits;

use Bitrewards\ReferralTool\DTO\FormData;
use Bitrewards\ReferralTool\Fields\FieldGroup;
use Illuminate\Support\Facades\Validator;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\FieldCollection;
use Laravel\Nova\Http\Requests\NovaRequest;

trait CustomFormTrait
{
    protected function resolveFields(iterable $fields): FieldCollection
    {
        return (new FieldCollection($fields))
            ->map(static function ($item) {
                return $item instanceof FieldGroup ? $item->fields() : $item;
            })
            ->flatten();
    }

    protected function fillValues(iterable $fields, array $values): FieldCollection
    {
        return (new FieldCollection($fields))
            ->map(static function (Field $field) use ($values) {
                if (null === $field->value && isset($values[$field->attribute])) {
                    $field->value = $values[$field->attribute];
                }

                return $field;
            });
    }

    protected function applyRequest(NovaRequest $request, iterable $fields, array $defaults = []): FieldCollection
    {
        $fields = $this->resolveFields($fields);
        $fields = $this->fillValues($fields, $defaults);
        $fields = collect($fields)
            ->each(static function (Field $field) use ($request) {
                if ($request->has($field->attribute)) {
                    $field->value = $request->get($field->attribute);
                }

                return $field;
            });

        return new FieldCollection($fields);
    }

    protected function validationRules(NovaRequest $request, iterable $fields): array
    {
        $r = collect($fields)
            ->mapWithKeys(static function (Field $field) use ($request) {
                return $field->getRules($request);
            })
            ->filter(static function ($rules) {
                return !empty($rules);
            })
            ->all();

        return $r;
    }

    protected function validate(NovaRequest $request, iterable $fields): FieldCollection
    {
        Validator::make($request->all(), $this->validationRules($request, $fields))->validate();

        return new FieldCollection($fields);
    }

    protected function formData(NovaRequest $request, array $fields, array $defaults = []): FormData
    {
        $formFields = $this->applyRequest(
            $request,
            $fields,
            $defaults
        );

        $this->validate($request, $formFields);

        return new FormData($formFields, $defaults);
    }
}
