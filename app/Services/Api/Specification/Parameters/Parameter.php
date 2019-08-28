<?php

namespace App\Services\Api\Specification\Parameters;

use Illuminate\Contracts\Support\Arrayable;
use App\Services\Api\Specification\PayloadTrait;

abstract class Parameter implements Arrayable
{
    use PayloadTrait;

    /**
     * Raw data.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Key names for optional settings.
     * These keys will be collected in Parameter::toArray method.
     *
     * @var array
     */
    protected $optionalSettingsKeys = [
        'collectionFormat', 'uniqueItems', 'allowEmptyValue',
        'default', 'maximum', 'exclusiveMaximum', 'minimum',
        'exclusiveMinimum', 'maxLength', 'minLength', 'pattern',
        'maxItems', 'minItems', 'uniqueItems', 'multipleOf',
    ];

    /**
     * Create new parameter instance.
     *
     * @param string $name        parameter name
     * @param string $description parameter description
     */
    public function __construct(string $name, string $description)
    {
        $this->attachData('name', $name)
            ->attachData('description', $description)
            ->attachData('type', $this->type());
    }

    /**
     * Get the location of the parameter.
     * Possible values are "query", "header", "path", "formData" or "body".
     *
     * @return string
     */
    abstract public function in();

    /**
     * Get parameter type.
     * Possible values are "string", "number", "integer", "boolean", "array" or "file".
     *
     * @return string
     */
    abstract public function type();

    /**
     * Boot new parameter.
     *
     * @return static|null
     */
    protected function boot()
    {
        return $this;
    }

    /**
     * Indicates that current parameter is required.
     *
     * @return static
     */
    public function required()
    {
        return $this->attachData('required', true);
    }

    /**
     * Indicates that current parameter accepts values only from given enum.
     *
     * @param array $enum
     *
     * @return static
     */
    public function usingEnum(array $enum)
    {
        return $this->attachData('enum', $enum);
    }

    /**
     * Get the array representation of parameter.
     *
     * @return array
     */
    public function toArray()
    {
        $this->clearPayload()
            ->attachIfExists('name', 'name')
            ->attachIfExists('description', 'description')
            ->attachPayload('in', $this->in())
            ->attachPayload('type', $this->type())
            ->attachIfExists('required', 'required')
            ->attachIfExists('schema', 'schema')
            ->attachIfExists('enum', 'enum')
            ->attachIfExists('items', 'items')
            ->attachOptionalSettings();

        return $this->payload();
    }

    /**
     * Get optional settings values.
     *
     * @return static
     */
    protected function attachOptionalSettings()
    {
        // Since some parameters may have additional settings, such as
        // items format or unique values flag, we need to find them
        // and attach to the parameter's array representation.

        foreach ($this->optionalSettingsKeys as $key) {
            $this->attachIfExists($key, $key);
        }

        return $this;
    }
}
