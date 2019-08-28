<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\DatePeriodRequest;
use App\Models\Partner;
use App\Services\ReferralStatisticService;
use App\User;
use Illuminate\Http\JsonResponse;

class ReferralStatisticController extends BaseController
{
    /**
     * @var ReferralStatisticService
     */
    protected $referralStatisticService;

    public function __construct(ReferralStatisticService $referralStatisticService)
    {
        $this->referralStatisticService = $referralStatisticService;
    }

    public function referralStatistic(DatePeriodRequest $request): JsonResponse
    {
        /**
         * @todo implement
         */
        $statistic = $this->referralStatisticService->getPartnerReferralStatistic(
            new Partner(),
            $request->getFrom(),
            $request->getTo()
        );

        return $this->responseJson($statistic);
    }

    public function getReferralsPurchasesAmountDailyStatistic(DatePeriodRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = \Auth::user();

        $data = $this->referralStatisticService->getReferralsPurchasesAmountTrendData(
            $user->partner,
            $request->getFrom(),
            $request->getTo()
        );

        return $this->responseJson($data);
    }

    public function getReferralsPurchasesCountDailyStatistic(DatePeriodRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = \Auth::user();

        $data = $this->referralStatisticService->getReferralsPurchasesCountTrendData(
            $user->partner,
            $request->getFrom(),
            $request->getTo()
        );

        return $this->responseJson($data);
    }

    public function getUniqueReferralsCountDailyStatistic(DatePeriodRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = \Auth::user();

        $data = $this->referralStatisticService->getUniqueReferralsCountTrendData(
            $user->partner,
            $request->getFrom(),
            $request->getTo()
        );

        return $this->responseJson($data);
    }
}
