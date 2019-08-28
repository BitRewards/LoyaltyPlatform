<?php

namespace App\Http\Controllers\Dashboard;

use App\Services\UserService;
use Illuminate\Http\Request;
use Laravel\Nova\Http\Controllers\LoginController as NovaLoginController;

class LoginController extends NovaLoginController
{
    protected function credentials(Request $request): array
    {
        $credentials = parent::credentials($request);
        $credentials['password'] .= UserService::STATIC_PASSWORD_SALT;

        return $credentials;
    }
}
