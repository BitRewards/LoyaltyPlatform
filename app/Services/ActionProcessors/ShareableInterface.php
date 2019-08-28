<?php

namespace App\Services\ActionProcessors;

use App\Administrator;

interface ShareableInterface
{
    public function getUrl(Administrator $user);
}
