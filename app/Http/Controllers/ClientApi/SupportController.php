<?php

namespace App\Http\Controllers\ClientApi;

use App\DTO\PartnerPage\HelpItemDataFactory;
use App\Http\Controllers\ClientApiController;
use App\Http\Requests\Support;
use App\Models\Partner;
use App\Services\SupportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class SupportController extends ClientApiController
{
    /**
     * @var SupportService
     */
    private $supportService;

    /**
     * @var HelpItemDataFactory
     */
    private $helpItemDataFactory;

    public function __construct(
        SupportService $supportService,
        HelpItemDataFactory $helpItemDataFactory
    ) {
        $this->supportService = $supportService;
        $this->helpItemDataFactory = $helpItemDataFactory;
    }

    public function sendQuestion(Support $request): Response
    {
        $this->supportService->sendQuestion(
            $request->user(),
            $request->message,
            $request->email
        );

        return $this->responseOk();
    }

    public function faqList(Partner $partner): JsonResponse
    {
        $faq = $this->helpItemDataFactory->factory($partner);

        return $this->responseJsonCollection($faq);
    }
}
