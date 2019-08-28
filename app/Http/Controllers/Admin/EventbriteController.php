<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\EventbriteService;
use Illuminate\Http\Request;

class EventbriteController extends Controller
{
    public function oauthRedirect(Request $request)
    {
        if (!empty($_GET['code'])) {
            app(EventbriteService::class)->handleOauthRedirect(\Auth::user()->partner, $_GET['code']);
        }

        \Alert::success(__('Eventbrite account successfully binded!'))->flash();

        return redirect(routePartner(\Auth::user()->partner, 'admin'));
    }

    public function unbind()
    {
        \Auth::user()->partner->eventbrite_oauth_token = null;
        \Auth::user()->partner->eventbrite_url = null;
        \Auth::user()->partner->save();

        \Alert::success(__('Eventbrite account unbinded'))->flash();

        return redirect(routePartner(\Auth::user()->partner, 'admin'));
    }
}
