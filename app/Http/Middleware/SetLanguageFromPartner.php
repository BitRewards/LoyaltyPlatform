<?php

namespace App\Http\Middleware;

use App\Models\Partner;
use Closure;

class SetLanguageFromPartner
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $languageSet = false;

        if (\Auth::user()) {
            if (\Auth::user()->partner_id) {
                \HLanguage::setLanguage(\Auth::user()->partner->default_language);
                \HContext::setPartner(\Auth::user()->partner);
                $languageSet = true;
            }
        }

        if (!$languageSet) {
            if ($request->partner instanceof Partner) {
                \HLanguage::setLanguage($request->partner->default_language);
                \HContext::setPartner($request->partner);
                $languageSet = true;
            }
        }

        if (!$languageSet) {
            \HLanguage::setLanguage(\HLanguage::LANGUAGE_EN);
        }

        return $next($request);
    }
}
