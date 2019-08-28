<?php

namespace App\Http\Controllers;

class AdminController extends Controller
{
    /**
     * Redirect to the dashboard.
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function redirect()
    {
        \Auth::shouldUse('admin');

        if (!\Auth::user()) {
            return redirect('admin/login');
        }

        if (\Auth::user()->can('admin')) {
            return redirect(url('admin/partner'));
        } else {
            return redirect(url('admin/user'));
        }
    }
}
