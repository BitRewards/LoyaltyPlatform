<?php

namespace Bitrewards\ReferralTool\DTO;

use Laravel\Nova\Fields\FieldCollection;

class FormData
{
    /**
     * @var FieldCollection
     */
    protected $fields;

    /**
     * @var array
     */
    protected $defaults;

    public function __construct(FieldCollection $fields, array $defaults)
    {
        $this->fields = $fields;
        $this->defaults = $defaults;
    }

    public function hasChanged(...$attributes): bool
    {
        foreach ($attributes as $attr) {
            $field = $this->fields->findFieldByAttribute($attr);
            $default = $this->defaults[$attr] ?? null;

            if ($field && $field->value !== $default) {
                return true;
            }
        }

        return false;
    }

    public function get(string $attribute, $default = null)
    {
        return $this->fields->findFieldByAttribute($attribute)->value ?? $default;
    }

    public function getMultiple(...$keys): array
    {
        $values = [];

        foreach ($keys as $key) {
            $values[$key] = $this->get($key);
        }

        return $values;
    }
}
