<?php

namespace App\Enums;

class ErrorCode
{
    /**
     * Please, don't use http codes! They are reserved.
     */
    const UNKNOWN_ERROR = 1001;
    const VALIDATION_ERROR = 1002;
}
