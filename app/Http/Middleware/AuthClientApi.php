<?php

namespace App\Http\Middleware;

use App\Services\AuthTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthClientApi
{
    /**
     * @var AuthTokenService
     */
    private $authTokenService;

    public function __construct(AuthTokenService $authTokenService)
    {
        $this->authTokenService = $authTokenService;
    }

    public function handle(Request $request, \Closure $next)
    {
        if ($token = $request->header(\HApp::HEADER_AUTH_TOKEN)) {
            $authToken = $this->authTokenService->findAuthToken($token);

            if ($authToken && !$authToken->isExpired()) {
                Auth::login($authToken->user);
            }
        }

        return $next($request);
    }
}
