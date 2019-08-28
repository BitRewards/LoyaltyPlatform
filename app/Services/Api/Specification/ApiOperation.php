<?php

namespace App\Services\Api\Specification;

use Closure;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Arrayable;
use App\Services\Api\Specification\Responses\Response;

class ApiOperation implements Arrayable
{
    use PayloadTrait;

    /**
     * Create new API operation instance.
     *
     * @param array $data = null Path data
     */
    public function __construct(array $data = null)
    {
        if (!is_null($data)) {
            $this->data = $data;
        }
    }

    /**
     * Set operation's shared parameters. Given parameters
     * will be prepended to the current parameters list.
     *
     * @param array $parameters
     *
     * @return $this
     */
    public function setSharedParameters(array $parameters)
    {
        if (!isset($this->data['parameters'])) {
            $this->data['parameters'] = [];
        }

        // Since in most cases path parameters represents
        // item identificators or something similar, it's
        // worth it to push them at the array beginning.

        // Also, we're iterating through reversed array
        // so if we're adding several parameters, they
        // will be pushed to array in original order.

        foreach (array_reverse($parameters) as $parameter) {
            array_unshift($this->data['parameters'], $parameter);
        }

        return $this;
    }

    /**
     * Generate operation payload and return YAML representation.
     *
     * @return string YAML representation of current API operation
     */
    public function dump(): string
    {
        return Yaml::dump($this->toArray());
    }

    /**
     * Get array representation of current API operation.
     *
     * @return array
     */
    public function toArray(): array
    {
        // Here we're attaching few payload fields that
        // either required by Open API Specification
        // or may be ommited if they don't exist.

        $this->clearPayload()
            ->attachIfExists('summary', 'summary')
            ->attachIfExists('description', 'description')
            ->attachIfExists('consumes', 'consumes', function ($value) {
                return is_array($value) ? $value : [$value];
            })
            ->attachIfExists('produces', 'produces', function ($value) {
                return is_array($value) ? $value : [$value];
            })
            ->attachParameters()
            ->attachIfExists('tags', 'tags')
            ->attachIfExists('externalDocs', 'externalDocs')
            ->attachIfExists('operationId', 'operationId')
            ->attachIfExists('schemes', 'schemes', function ($value) {
                return is_array($value) ? $value : [$value];
            })
            ->attachIfExists('deprecated', 'deprecated')
            ->attachIfExists('security', 'security')
            ->attachResponses();

        return $this->payload();
    }

    /**
     * Get operation's HTTP method.
     *
     * @return string HTTP methd ('get' by default)
     */
    public function method(): string
    {
        return strtolower($this->data['method'] ?? 'get');
    }

    /**
     * Attach operation parameters (if exists) to final payload.
     *
     * @return static
     */
    protected function attachParameters()
    {
        return $this->attachArrayDataToPayload('parameters', 'parameters');
    }

    /**
     * Attach operation responses (if exists) to final payload.
     *
     * @return static
     */
    protected function attachResponses()
    {
        return $this->attachArrayDataToPayload('responses', 'responses', function (Collection $items) {
            return $items->keyBy(function (Response $response) {
                return $response->status();
            });
        });
    }

    /**
     * Get array representation of data items and attach them to final payload.
     *
     * @param string   $dataKey
     * @param string   $payloadKey
     * @param \Closure $callback   = null Callback executes before toArray() conversion
     *
     * @return static
     */
    protected function attachArrayDataToPayload(string $dataKey, string $payloadKey, Closure $callback = null)
    {
        if (empty($this->data[$dataKey])) {
            return $this;
        }

        $items = collect($this->data[$dataKey]);

        if (is_callable($callback)) {
            $items = $callback($items);
        }

        return $this->attachPayload($payloadKey, $items->toArray());
    }
}
