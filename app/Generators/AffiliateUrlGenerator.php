<?php

namespace App\Generators;

use App\Generators\AffiliateUrlGenerator\ProviderInterface;
use App\Models\Action;
use App\Models\PersonInterface;

class AffiliateUrlGenerator
{
    /**
     * @var ProviderInterface[]
     */
    protected $providers = [];

    public function setProviders(array $providers)
    {
        $this->providers = $providers;
    }

    public function addProvider(ProviderInterface $provider)
    {
        $this->providers[] = $provider;
    }

    public function generate(Action $action, PersonInterface $person = null): ?string
    {
        if (!$person) {
            return null;
        }

        foreach ($this->providers as $provider) {
            if ($provider->isSupported($action)) {
                return $provider->generate($action, $person);
            }
        }

        throw new \InvalidArgumentException("Action type='{$action->type}' not supported");
    }
}
