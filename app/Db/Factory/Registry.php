<?php

namespace App\Db\Factory;

use App\Models\PartnerGroup;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method PartnerGroup        getPartnerGroup(array $states = [], string $name = 'default')
 * @method \Closure            lazyPartnerGroupId(array $states = [], string $name = 'default')
 * @method \App\Models\Partner getPartner(array $states = [], string $name = 'default')
 * @method \Closure            lazyPartnerId(array $states = [], string $name = 'default')
 * @method \App\Models\Person  getPerson(array $states = [], string $name = 'default')
 * @method \Closure            lazyPersonId(array $states = [], string $name = 'default')
 * @method \App\Models\User    getUser(array $states = [], string $name = 'default')
 * @method \Closure            lazyUserId(array $states = [], string $name = 'default')
 * @method \App\Models\Action  getAction(array $states = [], string $name = 'default')
 * @method \Closure            lazyActionId(array $states = [], string $name = 'default')
 * @method \App\Models\Action  getReward(array $states = [], string $name = 'default')
 * @method \Closure            lazyRewardId(array $states = [], string $name = 'default')
 * @method \App\Models\Action  getAdministrator(array $states = [], string $name = 'default')
 * @method \Closure            lazyAdministratorId(array $states = [], string $name = 'default')
 */
final class Registry
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var Model[]
     */
    private $cache = [];

    private $modelNamespaces = [
        'App\\Models',
        'App',
    ];

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    private function cacheKey(string $modelClass, array $states, string $name): string
    {
        $statesString = implode(':', $states);

        return "{$modelClass}.{$name}.{$statesString}";
    }

    private function getCached(string $modelClass, array $states, string $name): ?Model
    {
        $cacheKey = $this->cacheKey($modelClass, $states, $name);

        return $this->cache[$cacheKey] ?? null;
    }

    private function get(string $modelClass, array $states = [], string $name = 'default'): Model
    {
        $model = $this->getCached($modelClass, $states, $name);

        if (!$model) {
            $model = $this->factory->of($modelClass)->states($states)->create();
            $this->put($model, $states, $name);
        }

        return $model;
    }

    private function getLazyId(string $modelClass, array $states = [], string $name = 'default'): \Closure
    {
        return function () use ($modelClass, $states, $name) {
            return $this->get($modelClass, $states, $name)->id;
        };
    }

    private function getModelClass(string $modelName): ?string
    {
        foreach ($this->modelNamespaces as $namespace) {
            $modelClass = "$namespace\\$modelName";

            if (class_exists($modelClass)) {
                return $modelClass;
            }
        }

        return null;
    }

    public function put(Model $model, array $states = [], string $name = 'default'): void
    {
        $key = $this->cacheKey(get_class($model), $states, $name);

        $this->cache[$key] = $model;
    }

    public function __call($name, $arguments)
    {
        if (0 === strpos($name, 'get')) {
            $method = 'get';
            $modelName = substr($name, 3);
        } elseif (0 === strpos($name, 'lazy') && 'Id' === substr($name, -2)) {
            $method = 'getLazyId';
            $modelName = substr($name, 4, -2);
        } else {
            throw new \LogicException("Method {$name} not exist");
        }

        $modelClass = $this->getModelClass($modelName);

        if (!$modelClass) {
            throw new \InvalidArgumentException("Model {$modelName} not found");
        }

        return $this->$method($modelClass, ...$arguments);
    }
}
