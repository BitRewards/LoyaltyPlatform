<?php

namespace App\Http\Requests\Traits;

use App\Models\User;
use Illuminate\Contracts\Support\MessageBag;

trait UserByEmail
{
    private $userByEmail;

    public function validateEmail(MessageBag $messageBag)
    {
        if (!$this->email) {
            return;
        }
        $this->userByEmail = User::model()->findByPartnerAndEmail($this->user()->partner, $this->email);
    }

    /**
     * @return User|null
     */
    public function getUserByEmail()
    {
        return $this->userByEmail;
    }
}
