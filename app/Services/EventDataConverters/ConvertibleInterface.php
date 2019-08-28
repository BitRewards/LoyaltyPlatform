<?php

namespace App\Services\EventDataConverters;

use Carbon\Carbon;

interface ConvertibleInterface
{
    public function getConvertedData();

    public function getEntityExternalId();

    public function getExternalEventCreatedAt(): ?Carbon;
}
