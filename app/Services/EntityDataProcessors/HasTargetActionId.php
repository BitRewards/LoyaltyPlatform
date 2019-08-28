<?php

namespace App\Services\EntityDataProcessors;

interface HasTargetActionId
{
    public function getTargetActionId(): ?int;
}
