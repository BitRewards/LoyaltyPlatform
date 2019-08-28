<?php

namespace App\Http\Controllers\Admin;

use App\Administrator;
use App\Http\Controllers\Controller;
use App\Models\Partner;

// VALIDATION: change the requests to match your own file names if you need form validation

class CashierController extends Controller
{
    public function index()
    {
        if (!isset($_GET['api_token'])) {
            return redirect(route('admin.login'));
        }

        $user = Administrator::where('api_token', $_GET['api_token'])->first();

        if (!$user || !$user->partner) {
            return redirect(route('admin.login'));
        }

        if ($user->partner->isBitrewardsEnabled() && !$user->partner->getSetting(Partner::SETTINGS_IS_CASHIER_ENABLED_FOR_BITREWARDS) && !isset($_GET['bypass-bitrewards-check'])) {
            return redirect(route('admin.login'));
        }

        \HLanguage::setLanguage($user->partner->default_language);

        return view('cashier.app', ['partner' => $user->partner]);
    }
}
