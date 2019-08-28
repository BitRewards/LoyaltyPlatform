<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public function create(User $user, $type, $text = null)
    {
        \DB::beginTransaction();

        $notification = new Notification();

        $notification->user_id = $user->id;
        $notification->type = $type;
        $notification->text = $text;

        $notification->saveOrFail();

        \DB::commit();

        return $notification;
    }
}
