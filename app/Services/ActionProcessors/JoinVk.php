<?php

namespace App\Services\ActionProcessors;

use App\Models\StoreEvent;

class JoinVk extends Base
{
    protected $requiresEntity = false;
    protected $requiresEntityConfirmation = false;

    public function getEventAction()
    {
        return StoreEvent::ACTION_JOIN_VK;
    }

    public function getLimitPerUser()
    {
        return 1;
    }

    public function getGroupId()
    {
        $groupId = $this->getSetting('group-id');

        if ($groupId) {
            return $groupId;
        }
        $path = \HUrl::getPath($this->getSetting('url'));
        $groupShortName = trim($path, ' /');

        $apiRequestParams = [
            'access_token' => config('vk.service_access_token'),
            'v' => '5.80',
            'group_id' => $groupShortName,
        ];

        $response = @file_get_contents(
            'https://api.vk.com/method/groups.getById?'.http_build_query($apiRequestParams)
        );
        $response = \HJson::decode($response);
        $groupId = $response['response'][0]['id'] ?? null;

        if ($groupId) {
            $this->setSetting('group-id', $groupId);
        }

        return $groupId;
    }
}
