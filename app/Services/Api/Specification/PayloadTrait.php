<?php

namespace App\Services\Api\Specification;

use Closure;

trait PayloadTrait
{
    /**
     * @var array
     */
    protected $payload = [];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Remove all payload data.
     *
     * @return static
     */
    protected function clearPayload()
    {
        $this->payload = [];

        return $this;
    }

    /**
     * Attach given data to current payload at given key using "dot" notation.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return static
     */
    protected function attachPayload(string $key, $value)
    {
        array_set($this->payload, $key, $value);

        return $this;
    }

    /**
     * Attach given data to current data at given key using "dot" notation.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return static
     */
    protected function attachData(string $key, $value)
    {
        array_set($this->data, $key, $value);

        return $this;
    }

    /**
     * Attach data key (if exists) to final payload.
     *
     * @param string   $dataKey     key name to find in $this->data array
     * @param string   $payloadKey  final payload key
     * @param \Closure $transformer = null Value transformer
     *
     * @return static
     */
    protected function attachIfExists(string $dataKey, string $payloadKey, Closure $transformer = null)
    {
        if (!isset($this->data[$dataKey])) {
            return $this;
        }

        if (is_callable($transformer)) {
            $this->data[$dataKey] = $transformer($this->data[$dataKey]);
        }

        return $this->attachPayload($payloadKey, $this->data[$dataKey]);
    }

    /**
     * Determines if current payload has given key.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasPayload(string $key): bool
    {
        return isset($this->payload[$key]);
    }

    /**
     * Determines if current data has given key.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasData(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * Get the payload data.
     * If no key is given, the entire payload will be returned.
     *
     * @param string $key     = null
     * @param mixed  $default = null
     *
     * @return mixed
     */
    public function payload(string $key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->payload;
        }

        return $this->hasPayload($key) ? $this->payload[$key] : $default;
    }
}
