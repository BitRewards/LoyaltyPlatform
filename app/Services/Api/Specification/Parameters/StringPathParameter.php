<?php

namespace App\Services\Api\Specification\Parameters;

use App\Services\Api\Specification\Parameters\Traits\StringTrait;
use App\Services\Api\Specification\Parameters\Traits\InPathTrait;

class StringPathParameter extends Parameter
{
    use StringTrait, InPathTrait;
}
