<?php

namespace App\Services\EntityDataProcessors;

class Base extends DataProcessorAbstract
{
    public function couldBeAutoConfirmed(): bool
    {
        return true;
    }
}
