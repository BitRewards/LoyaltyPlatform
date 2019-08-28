<?php

namespace App\Http\Controllers;

use App\Administrator;
use App\Mail\BalanceChanged;
use App\Mail\Test;
use App\Mail\ResetPassword;
use App\Mail\ConfirmEmail;
use App\Mail\UsersBulkImport;
use App\Models\Partner;
use App\Services\UserService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Input;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\DTO\UsersBulkImport\ImportReport;

class EmailTestController extends BaseController
{
    const DEBUG_USER_KEY = 'Os0qKUxUw4';
    const DEBUG_ADMINISTRATOR_ID = 140;

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        if (!(\App::isLocal() || ($_COOKIE['email-test'] ?? null) == 'chae4Eex')) {
            throw new HttpException(404);
        }
    }

    public function example()
    {
        $mailable = new Test(app(UserService::class)->getTestClientNoRole());

        return $mailable->renderHtml();
    }

    public function resetPassword()
    {
        $mailable = new ResetPassword(app(UserService::class)->getTestClientNoRole(), 'token');

        return $mailable->renderHtml();
    }

    public function confirmEmail()
    {
        $mailable = new ConfirmEmail(app(UserService::class)->findByKey(self::DEBUG_USER_KEY), 'token');

        return $mailable->renderHtml();
    }

    public function balanceChanged()
    {
        \HLanguage::setLanguage(Input::get('language', \HLanguage::LANGUAGE_RU));
        $partnerKey = \Request::get('partner_key');
        $partner = $partnerKey ? Partner::model()->findByKey($partnerKey) : null;

        if ($partner) {
            \HLanguage::setLanguage($partner->default_language);
        }

        // using real user after deprecating Mockery, you can modify the key :
        $mailable = new BalanceChanged(app(UserService::class)->findByKey(self::DEBUG_USER_KEY), 213);

        return $mailable->renderHtml();
    }

    public function usersBulkImport()
    {
        $administrator = Administrator::find(self::DEBUG_ADMINISTRATOR_ID);
        $mailable = new UsersBulkImport($administrator, new ImportReport());

        return $mailable->renderHtml();
    }
}
