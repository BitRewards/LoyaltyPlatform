<?php

namespace App\Services\Api\Specification\Parameters;

use App\Services\Api\Specification\Parameters\Traits\StringTrait;
use App\Services\Api\Specification\Parameters\Traits\InQueryTrait;

class StringQueryParameter extends Parameter
{
    use StringTrait, InQueryTrait;
}
