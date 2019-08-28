<?php

namespace App\Http\Controllers\ClientApi;

use App\DTO\Factory\SpecialOfferActionFactory;
use App\DTO\Factory\SpecialOfferRewardFactory;
use App\Http\Controllers\ClientApiController;
use App\Models\PersonInterface;
use App\Services\SpecialOfferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SpecialOfferController extends ClientApiController
{
    /**
     * @var SpecialOfferService
     */
    protected $specialOfferService;

    /**
     * @var SpecialOfferActionFactory
     */
    protected $specialOfferActionFactory;

    /**
     * @var SpecialOfferRewardFactory
     */
    protected $specialOfferRewardFactory;

    /**
     * @var PersonInterface
     */
    private $currentPerson;

    public function __construct(
        SpecialOfferService $specialOfferService,
        SpecialOfferActionFactory $specialOfferActionFactory,
        SpecialOfferRewardFactory $specialOfferRewardFactory,
        PersonInterface $currentPerson = null
    ) {
        $this->specialOfferService = $specialOfferService;
        $this->specialOfferActionFactory = $specialOfferActionFactory;
        $this->specialOfferRewardFactory = $specialOfferRewardFactory;
        $this->currentPerson = $currentPerson;
    }

    public function actionList(Request $request): JsonResponse
    {
        $specialOfferActions = $this
            ->specialOfferService
            ->getActionList(...$this->getPageParameters($request));

        return $this->responseJsonCollection(
            $this->specialOfferActionFactory->factoryCollection($specialOfferActions, $this->currentPerson),
            $this->specialOfferService->getActionsCount()
        );
    }

    public function rewardList(Request $request): JsonResponse
    {
        $offers = $this
            ->specialOfferService
            ->getRewardList(...$this->getPageParameters($request));

        return $this->responseJsonCollection(
            $this->specialOfferRewardFactory->factoryCollection($offers),
            $this->specialOfferService->getRewardsCount()
        );
    }
}
