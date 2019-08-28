<?php

namespace App\Http\Requests\Traits;

use App\Models\User;
use Illuminate\Contracts\Support\MessageBag;

trait UserByKey
{
    private $userByKey;

    public function validateUserKey(MessageBag $messageBag)
    {
        $userKey = $this->route('userKey') ?? $this->user_key;

        if (!$userKey) {
            return;
        }

        $this->userByKey = User::where('key', $userKey)->first();

        if (!$this->userByKey) {
            $messageBag->add('user_key', 'User not found');

            return;
        }

        if ($this->userByKey->partner_id != $this->user()->partner_id && !$this->user()->can('admin')) {
            $messageBag->add('user_key', 'Permission Denied');

            return;
        }
    }

    /**
     * @return User|null
     */
    public function getUserByKey()
    {
        return $this->userByKey;
    }
}
