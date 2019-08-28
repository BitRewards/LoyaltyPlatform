<?php

namespace App\Http\Requests\Traits;

use App\Models\User;
use Illuminate\Contracts\Support\MessageBag;

trait UserByPhone
{
    private $userByPhone;

    public function validatePhone(MessageBag $messageBag)
    {
        if (!$this->phone) {
            return;
        }

        $this->userByPhone = User::model()->findByPartnerAndPhone($this->user()->partner, $this->phone);
    }

    /**
     * @return User|null
     */
    public function getUserByPhone()
    {
        return $this->userByPhone;
    }
}
