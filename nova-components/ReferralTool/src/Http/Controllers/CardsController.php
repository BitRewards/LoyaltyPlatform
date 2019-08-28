<?php

namespace Bitrewards\ReferralTool\Http\Controllers;

use Bitrewards\ReferralTool\ToolServiceProvider;
use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;

class CardsController extends Controller
{
    public function cards(NovaRequest $request)
    {
        return collect(ToolServiceProvider::cards())
            ->filter->authorize($request)
            ->values();
    }
}
