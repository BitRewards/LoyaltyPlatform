<?php

namespace App\Generators\AffiliateUrlGenerator;

use App\Models\Action;
use App\Models\PersonInterface;

interface ProviderInterface
{
    public function isSupported(Action $action): bool;

    public function generate(Action $action, PersonInterface $person): ?string;
}
