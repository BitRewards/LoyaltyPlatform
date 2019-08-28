<?php

namespace App\Http\Controllers\ClientApi;

use App\DTO\Factory\PartnerFactory;
use App\DTO\PartnerPageDataFactory;
use App\Fractal\Transformer\PartnerPageDataTransformer;
use App\Http\Controllers\ClientApiController;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class PartnerController extends ClientApiController
{
    /**
     * @var PartnerFactory
     */
    private $partnerFactory;

    /**
     * @var PartnerPageDataFactory
     */
    private $partnerPageDataFactory;

    /**
     * @var PartnerPageDataTransformer
     */
    private $partnerPageDataTransformer;

    public function __construct(
        PartnerFactory $partnerFactory,
        PartnerPageDataFactory $partnerPageDataFactory,
        PartnerPageDataTransformer $partnerPageDataTransformer
    ) {
        $this->partnerFactory = $partnerFactory;
        $this->partnerPageDataFactory = $partnerPageDataFactory;
        $this->partnerPageDataTransformer = $partnerPageDataTransformer;
    }

    public function get(Partner $partner): JsonResponse
    {
        $partnerData = $this->partnerFactory->factory($partner);

        return $this->responseJson($partnerData);
    }

    public function mainPage(Request $request, Partner $partner): JsonResponse
    {
        $overriddenTitle = $request->get('title');
        $tags = [Input::get('tag', ''), '*'];
        $partnerPageData = $this->partnerPageDataFactory->factory(
            $partner,
            \Auth::user(),
            $overriddenTitle,
            $tags
        );

        return $this->responseJson(fractal(
            $partnerPageData,
            $this->partnerPageDataTransformer
        ));
    }
}
