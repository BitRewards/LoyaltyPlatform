<?php

namespace App\Services\ActionProcessors;

use App\Models\StoreEvent;

class Signup extends Base
{
    protected $requiresEntity = false;
    protected $requiresEntityConfirmation = false;

    public function getEventAction()
    {
        return StoreEvent::ACTION_SIGNUP;
    }

    public function getLimitPerUser()
    {
        return 1;
    }
}
