<?php

namespace Bitrewards\ReferralTool;

use App\Models\Partner;
use Illuminate\Http\Request;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class ReferralTool extends Tool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     */
    public function boot()
    {
        Nova::script('referral-tool', __DIR__.'/../dist/js/tool.js');
        Nova::style('referral-tool', __DIR__.'/../dist/css/tool.css');
    }

    /**
     * Build the view that renders the navigation links for the tool.
     *
     * @return \Illuminate\View\View
     */
    public function renderNavigation()
    {
        return view('referral-tool::navigation');
    }

    public function authorizedToSee(Request $request)
    {
        return parent::authorizedToSee($request) && \Auth::user()->partner;
    }

    public static function isEnabled(?Request $request = null): bool
    {
        /** @var Partner $partner */
        $partner = ($request ? $request->user() : \Auth::user())->partner ?? null;

        return $partner && $partner->isFiatReferralEnabled(false);
    }
}
