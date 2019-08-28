<?php

namespace App\Http\Controllers\Auth;

use App\Administrator;
use App\Models\Partner;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Reset the given user's password.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function reset(Request $request, $partner = null)
    {
        $this->validate($request, [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:7',
        ]);

        $request->query->add(['password_confirmation' => $request->password]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        if (Password::PASSWORD_RESET == $response) {
            $user = Administrator::where('email', $request->input('email'))->first();

            if (!is_null($user)) {
                $this->guard()->login($user, true);
            }
        } else {
            return $this->sendResetFailedResponse($request, $response);
        }

        return redirect(route('admin'));
    }

    /**
     * @param Request      $request
     * @param Partner|null $partner
     *
     * @return Partner|null
     */
    protected function resolvePartner(Request $request, Partner $partner = null)
    {
        if (!is_null($partner)) {
            return $partner;
        }

        $user = Administrator::where('email', $request->input('email'))->first();

        if (is_null($user)) {
            return null;
        }

        return $user->partner;
    }

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Reset the given user's password.
     *
     * @param \Illuminate\Contracts\Auth\CanResetPassword $user
     * @param string                                      $password
     */
    protected function resetPassword($user, $password)
    {
        $user->forceFill([
            'password' => \Hash::make($password.UserService::STATIC_PASSWORD_SALT),
        ])->save();

        $this->guard()->login($user);
    }

    public function broker()
    {
        return Password::broker('administrators');
    }

    protected function guard()
    {
        return \Auth::guard('admin');
    }
}
