<?php

namespace App\Services\Api\Specification\Parameters;

use App\Services\Api\Specification\Parameters\Traits\IntegerTrait;
use App\Services\Api\Specification\Parameters\Traits\InPathTrait;

class IntegerPathParameter extends Parameter
{
    use IntegerTrait, InPathTrait;
}
