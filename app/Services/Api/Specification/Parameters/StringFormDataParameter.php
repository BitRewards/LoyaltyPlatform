<?php

namespace App\Services\Api\Specification\Parameters;

use App\Services\Api\Specification\Parameters\Traits\StringTrait;
use App\Services\Api\Specification\Parameters\Traits\InFormDataTrait;

class StringFormDataParameter extends Parameter
{
    use StringTrait, InFormDataTrait;
}
