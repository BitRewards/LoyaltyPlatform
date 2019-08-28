<?php

namespace App\Services\Api\Specification\Parameters;

use App\Services\Api\Specification\Parameters\Traits\BooleanTrait;
use App\Services\Api\Specification\Parameters\Traits\InFormDataTrait;

class BooleanFormDataParameter extends Parameter
{
    use BooleanTrait, InFormDataTrait;
}
