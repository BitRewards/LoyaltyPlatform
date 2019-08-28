<?php

namespace App\Http\Middleware;

use App\Models\Token;
use App\Services\UserService;

class AutologinToken
{
    /**
     * @var UserService
     */
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        if ($token = $request->get(\HApp::PARAM_AUTOLOGIN_TOKEN)) {
            if ($token = Token::check($token, Token::TYPE_AUTO_LOGIN, null, null, null, false)) {
                if (\Auth::check()) {
                    \Auth::logout();
                }

                if ($token->owner) {
                    \Auth::login($token->owner);
                } else {
                    \Log::alert("No token owner for token = {$token->token}");
                }

                if ($token->isOwnerEmailDestination() && !$token->owner->isEmailConfirmed()) {
                    $this->userService->confirmEmailFor($token->owner);
                }

                if ($token->isOwnerPhoneDestination() && !$token->owner->isPhoneConfirmed()) {
                    $this->userService->confirmPhoneFor($token->owner);
                }
            }
        }

        return $next($request);
    }
}
