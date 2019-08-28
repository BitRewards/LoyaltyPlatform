<?php

namespace App\Services\Api\Specification\Parameters;

use App\Services\Api\Specification\Parameters\Traits\BooleanTrait;
use App\Services\Api\Specification\Parameters\Traits\InQueryTrait;

class BooleanQueryParameter extends Parameter
{
    use BooleanTrait, InQueryTrait;
}
