<?php

namespace App\Http\Controllers\Auth;

use App\Administrator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\UserService;

class LoginController extends Controller
{
    protected $data = []; // the information we send to the view

    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    // if not logged in redirect to
    protected $loginPath = 'admin/login';
    // after you've logged in redirect to
    protected $redirectTo = 'admin/';
    // after you've logged out redirect to
    protected $redirectAfterLogout = 'admin';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        \Auth::shouldUse('admin');
        // $this->middleware('guest', ['except' => 'logout', 'loginByApiToken']);
    }

    // -------------------------------------------------------
    // Laravel overwrites for loading backpack views
    // -------------------------------------------------------

    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        $this->data['title'] = trans('backpack::base.login'); // set the page title

        return view('backpack::auth.login', $this->data);
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->credentials($request);

        if ($this->guard()->attempt($credentials, $request->has('remember'))) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        $user = null;

        if ($id = $request->session()->get('previous_user_id')) {
            $user = Administrator::find($id);
        }

        $request->session()->flush();

        $request->session()->regenerate();

        if (isset($user)) {
            \Auth::login($user, true);
        }

        return redirect('/admin');
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    protected function credentials(Request $request)
    {
        $credentials = $request->only($this->username(), 'password');
        $credentials['password'] .= UserService::STATIC_PASSWORD_SALT;

        return $credentials;
    }

    public function loginByApiToken($token)
    {
        $user = Administrator::where('api_token', $token)->first();

        if (!$user) {
            return redirect(route('admin.login'));
        }

        if ($this->guard()->user()) {
            $this->guard()->logout();
        }

        $this->guard()->login($user, true);

        if (is_null($user->role) && !is_null($user->partner)) {
            return redirect(routePartner($user->partner, 'client.index'));
        }

        if ($redirectUrl = ($_GET['r'] ?? null)) {
            if (0 === strpos($redirectUrl, '/admin') || 0 === strpos($redirectUrl, '/dashboard')) {
                return redirect($redirectUrl);
            }
        }

        return redirect('/admin');
    }

    protected function guard()
    {
        return \Auth::guard('admin');
    }
}
