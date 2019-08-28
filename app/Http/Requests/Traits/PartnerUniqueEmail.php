<?php

namespace App\Http\Requests\Traits;

use App\Administrator;
use Illuminate\Contracts\Support\MessageBag;

trait PartnerUniqueEmail
{
    public function doExtraValidation(MessageBag $messageBag)
    {
        $existingUsersCount = Administrator::where([
            ['email', '=', $this->email],
            ['role', '=', Administrator::ROLE_PARTNER],
        ])->count();

        if ($existingUsersCount > 0) {
            $messageBag->add('email', _('This email has already been registered as a partner account'));
        }
    }
}
