<?php

namespace Bitrewards\ReferralTool\Fields;

use Laravel\Nova\Fields\Field;

class FieldGroup
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var Field[]
     */
    protected $fields = [];

    public function __construct(string $name = null, array $fields = [])
    {
        $this->setName($name);
        $this->setFields($fields);
    }

    public function setFields(array $fields): self
    {
        $this->fields = array_filter($fields, static function ($field) {
            return $field instanceof Field;
        });

        return $this->resolve();
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this->resolve();
    }

    protected function resolve(): self
    {
        foreach ($this->fields as $field) {
            $field->withMeta(['group' => $this->name]);
        }

        return $this;
    }

    public function fields(): array
    {
        return $this->fields;
    }
}
