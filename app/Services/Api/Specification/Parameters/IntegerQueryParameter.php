<?php

namespace App\Services\Api\Specification\Parameters;

use App\Services\Api\Specification\Parameters\Traits\InQueryTrait;
use App\Services\Api\Specification\Parameters\Traits\IntegerTrait;

class IntegerQueryParameter extends Parameter
{
    use IntegerTrait, InQueryTrait;
}
