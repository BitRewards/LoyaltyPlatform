<?php

namespace App\Services\ActionProcessors;

use App\Models\StoreEvent;

class ShareVk extends Base implements ShareableInterface
{
    protected $requiresEntity = false;
    protected $requiresEntityConfirmation = false;

    public function getEventAction()
    {
        return StoreEvent::ACTION_SHARE_VK;
    }

    public function getLimitPerUser()
    {
        return 1;
    }

    public function getUrl($user)
    {
        return $this->getSetting('url') ?: $user->referral_link;
    }
}
