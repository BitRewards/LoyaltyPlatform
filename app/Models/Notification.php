<?php

namespace App\Models;

class Notification extends AbstractModel
{
    public const TYPE_UNSPENT_BALANCE_REMINDER_FIRST = 'unspent-balance-reminder-first';
    public const TYPE_UNSPENT_BALANCE_REMINDER_SECOND = 'unspent-balance-reminder-second';
    public const TYPE_BURNING_POINTS_SUMMARY = 'burning-points-summary';

    protected $table = 'notifications';

    public function findByUser(User $user, $limit = 10)
    {
        return self::where(['user_id' => $user->id])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function findByUserAndType(User $user, $type)
    {
        return self::where(['user_id' => $user->id, 'type' => $type])
            ->orderBy('id', 'desc')
            ->first();
    }
}
