<?php

namespace App\Services\Api\Specification;

use Symfony\Component\Yaml\Yaml;
use App\Services\Api\ApiEndpoint;
use Illuminate\Contracts\Support\Arrayable;
use App\Services\Api\Definitions\ApiDefinitionInterface;
use App\Exceptions\Api\Specification\InvalidSchemaPayload;

class ApiSpecification implements Arrayable
{
    use ValidatorTrait;

    /**
     * The specification payload.
     *
     * @var array
     */
    protected $payload = [];

    /**
     * The specification endpoints.
     *
     * @var array
     */
    protected $endpoints = [];

    /**
     * The specification definitions.
     *
     * @var array
     */
    protected $definitions = [];

    /**
     * Create new specification instance.
     *
     * @param array $payload = []
     */
    public function __construct(array $payload = [])
    {
        $this->payload = $payload;
    }

    /**
     * Validation rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'swagger' => 'required|string',
            'info.title' => 'required|string',
            'info.version' => 'required',
            'paths' => 'required|array',
        ];
    }

    /**
     * Dump YAML string from schema payload.
     *
     * @throws \App\Exceptions\Api\Specification\InvalidSchemaPayload
     *
     * @return string
     */
    public function dump(): string
    {
        return Yaml::dump($this->toArray());
    }

    /**
     * Get full payload for current scheme.
     *
     * @throws \App\Exceptions\Api\Specification\InvalidSchemaPayload
     *
     * @return array
     */
    public function toArray()
    {
        if (!is_array($this->payload)) {
            throw new InvalidSchemaPayload('Given payload is not an array.');
        }

        $payload = array_merge($this->payload, $this->paths(), $this->definitions());

        $this->validate($payload);

        return $payload;
    }

    /**
     * Regsiter new API endpoint to current specification.
     *
     * @param \App\Services\Api\ApiEndpoint $endpoint
     *
     * @return static
     */
    public function registerEndpoint(ApiEndpoint $endpoint)
    {
        $this->endpoints[] = $endpoint;

        return $this;
    }

    /**
     * Register new API definition schema to current specification.
     *
     * @param \App\Services\Api\Definitions\ApiDefinitionInterface $definition
     *
     * @return static
     */
    public function registerDefinition(ApiDefinitionInterface $definition)
    {
        $this->definitions[] = $definition;

        return $this;
    }

    /**
     * Get the 'paths' section payload.
     *
     * @return array
     */
    protected function paths(): array
    {
        if (!count($this->endpoints)) {
            return [];
        }

        $paths = [];

        foreach ($this->endpoints as $endpoint) {
            $paths[$endpoint->path()] = $endpoint->toArray();
        }

        return ['paths' => $paths];
    }

    /**
     * Get the 'definitions' section payload.
     *
     * @return array
     */
    protected function definitions(): array
    {
        if (!count($this->definitions)) {
            return [];
        }

        $definitions = [];

        foreach ($this->definitions as $definition) {
            $definitions[$definition->name()] = $definition->toArray();
        }

        return ['definitions' => $definitions];
    }
}
