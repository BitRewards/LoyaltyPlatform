<?php

namespace App\Http\Controllers;

use App\Services\Api\ApiDocsGenerator;
use Illuminate\Http\Request;

class ApiDocsController extends Controller
{
    /**
     * Show API Docs for Russian locale.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function docsRu(Request $request)
    {
        return $this->docsResponse($request, 'ru');
    }

    /**
     * Show API Docs for English locale.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function docsEn(Request $request)
    {
        return $this->docsResponse($request, 'en');
    }

    /**
     * API Docs response.
     *
     * @param Request $request
     * @param string  $locale
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function docsResponse(Request $request, string $locale)
    {
        \HLanguage::setLanguage($locale);
        $tokenMessage = null;
        $user = $request->user();

        if (!is_null($user) && $user->can('partner')) {
            $tokenMessage = __('To execute API requests, specify your token in the field above: <strong>%s</strong>', $user->api_token);
        } else {
            $tokenMessage = __('Your API token is available in Integrations section of profile page');
        }

        return view('api.docs', [
            'apiTokenMessage' => $tokenMessage,
        ]);
    }

    /**
     * Get API Specification for RU locale.
     *
     * @param ApiDocsGenerator $docs
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function specificationRu(ApiDocsGenerator $docs)
    {
        return response($docs->getCachedSpecification('ru'), 200, ['Content-Type' => 'application/yaml']);
    }

    /**
     * Get API Specification for EN locale.
     *
     * @param ApiDocsGenerator $docs
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function specificationEn(ApiDocsGenerator $docs)
    {
        return response($docs->getCachedSpecification('en'), 200, ['Content-Type' => 'application/yaml']);
    }
}
