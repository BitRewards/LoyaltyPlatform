<?php

namespace App\Services\ActionProcessors;

use App\Models\StoreEvent;

class JoinFb extends Base
{
    protected $requiresEntity = false;
    protected $requiresEntityConfirmation = false;

    public function getEventAction()
    {
        return StoreEvent::ACTION_JOIN_FB;
    }

    public function getLimitPerUser()
    {
        return 1;
    }

    public function getPageUrl()
    {
        return $this->getSetting('url');
    }
}
