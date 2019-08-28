<?php

namespace App\Services\Api\Specification\Parameters;

use App\Services\Api\Specification\Parameters\Traits\FloatTrait;
use App\Services\Api\Specification\Parameters\Traits\InFormDataTrait;

class FloatFormDataParameter extends Parameter
{
    use FloatTrait, InFormDataTrait;
}
