<?php

namespace App\Services\Api\Specification\Parameters;

use App\Services\Api\Specification\Parameters\Traits\InFormDataTrait;
use App\Services\Api\Specification\Parameters\Traits\IntegerTrait;

class IntegerFormDataParameter extends Parameter
{
    use IntegerTrait, InFormDataTrait;
}
