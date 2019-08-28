<?php

namespace App\Services\Api\Specification\Responses;

use Illuminate\Contracts\Support\Arrayable;
use App\Services\Api\Specification\PayloadTrait;

abstract class Response implements Arrayable
{
    use PayloadTrait;

    /**
     * Create new Response instance.
     *
     * @param string $description response description
     */
    public function __construct(string $description)
    {
        $this->attachData('description', $description)
            ->attachData('status', 200);
    }

    /**
     * Set HTTP status code or status name.
     *
     * @param string|int $status HTTP code (200, 404, etc.) or status name ('default').
     *
     * @return static
     */
    public function withStatus($status)
    {
        return $this->attachData('status', $status);
    }

    /**
     * Attach given headers to current response.
     *
     * @param array $headers
     *
     * @return static
     */
    public function withHeaders(array $headers)
    {
        $headers = collect($headers)->map(function ($header) {
            return isset($header['description']) && isset($header['type']) ? $header : null;
        });

        return $this->attachData('headers', $headers->reject(null)->toArray());
    }

    /**
     * Indicates that current response uses single defined object (reference).
     *
     * @param string $reference referenced object name
     *
     * @return static
     */
    public function usingItemRef(string $reference)
    {
        return $this->attachData('schema', ['$ref' => '#/definitions/'.$reference]);
    }

    /**
     * Indicates that current response uses array of defined objects (references).
     *
     * @param string $reference referenced object name
     *
     * @return static
     */
    public function usingArrayRef(string $reference)
    {
        return $this->attachData('schema', [
            'type' => 'array',
            'items' => ['$ref' => '#/definitions/'.$reference],
        ]);
    }

    /**
     * Specify JSON object schema.
     *
     * @param array $schema
     *
     * @return static
     */
    public function usingSchema(array $schema)
    {
        return $this->attachData('schema', $schema);
    }

    /**
     * Get response status.
     *
     * @return string|int
     */
    public function status()
    {
        return $this->data['status'] ?? 'default';
    }

    /**
     * Get response description.
     *
     * @return string
     */
    public function description()
    {
        return $this->data['description'];
    }

    /**
     * Get the array representation of response.
     *
     * @return array
     */
    public function toArray()
    {
        $this->clearPayload()
            ->attachIfExists('description', 'description')
            ->attachIfExists('schema', 'schema')
            ->attachIfExists('headers', 'headers')
            ->attachIfExists('examples', 'examples');

        return $this->payload();
    }
}
