<?php

namespace App\Services\Api;

use InvalidArgumentException;
use App\Services\Api\Specification\ApiSpecification;

class ApiDocsGenerator
{
    const CACHE_PREFIX = 'api-specification-yml-';

    /**
     * API Endpoints.
     *
     * @var array
     */
    protected static $endpoints = [];

    /**
     * API Definitions.
     *
     * @var array
     */
    protected static $definitions = [];

    /**
     * Register endpoint classes.
     *
     * @param array $endpoints
     */
    public static function registerEndpoints(array $endpoints)
    {
        static::$endpoints = array_merge(static::$endpoints, $endpoints);
    }

    /**
     * Register definition classes.
     *
     * @param array $definitions
     */
    public static function registerDefinitions(array $definitions)
    {
        static::$definitions = array_merge(static::$definitions, $definitions);
    }

    /**
     * Generate docs.
     *
     * @param \App\Services\Api\Specification\ApiSpecification $specification
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function generate(ApiSpecification $specification)
    {
        if (!count(static::$endpoints)) {
            throw new InvalidArgumentException('You need to register any endpoint before calling generate method.');
        }

        foreach (static::$endpoints as $endpoint) {
            $specification->registerEndpoint(
                is_string($endpoint) ? new $endpoint() : $endpoint
            );
        }

        foreach (static::$definitions as $definition) {
            $specification->registerDefinition(
                is_string($definition) ? new $definition() : $definition
            );
        }

        return $specification->dump();
    }

    /**
     * Get cached specification for given locale.
     *
     * @param string $locale
     *
     * @return string
     */
    public function getCachedSpecification(string $locale)
    {
        return \Cache::remember(static::CACHE_PREFIX.$locale, \App::isLocal() ? 1 : 60 * 60, function () use ($locale) {
            return file_get_contents(public_path('/api/specification.'.$locale.'.yml'));
        });
    }

    /**
     * Remove cached specification for given locale.
     *
     * @param string $locale
     * @param bool   $rebuildCache = false
     *
     * @return $this
     */
    public function resetSpecificationCache(string $locale, bool $rebuildCache = false)
    {
        \Cache::forget(static::CACHE_PREFIX.$locale);

        if (true === $rebuildCache) {
            $this->getCachedSpecification($locale);
        }

        return $this;
    }
}
