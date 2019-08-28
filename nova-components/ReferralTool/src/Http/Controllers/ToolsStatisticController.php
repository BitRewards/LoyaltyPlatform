<?php

namespace Bitrewards\ReferralTool\Http\Controllers;

use App\DTO\ApiClient\ToolReportData;
use App\Models\Partner;
use App\Nova\Metrics\Tools\AverageChequeIncreaseValue;
use App\Nova\Metrics\Tools\AveragePurchaseAmountTrend;
use App\Nova\Metrics\Tools\ContactsTrend;
use App\Nova\Metrics\Tools\IssuedPromoCodesCountValue;
use App\Nova\Metrics\Tools\PartnerPurchasesAmountTrend;
use App\Nova\Metrics\Tools\PurchaseCountTrend;
use App\Nova\Metrics\Tools\SentEmailsCountTrend;
use App\Nova\Metrics\Tools\ToolsCountValue;
use App\Nova\Metrics\Tools\UniqueUsersCountValue;
use App\Nova\Metrics\Tools\UsedPromoCodesCountValue;
use App\Nova\Traits\MetricTrait;
use App\Services\Giftd\ApiClient;
use Bitrewards\ReferralTool\Cards\SimpleTable;
use Bitrewards\ReferralTool\Metrics\SocialPostsTrend;
use Bitrewards\ReferralTool\Traits\DefaultRangesTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;

class ToolsStatisticController extends Controller
{
    use DefaultRangesTrait;
    use MetricTrait;

    public function toolsStatistic(Request $request, ApiClient $clientFactory): JsonResponse
    {
        /** @var Partner $partner */
        $partner = $request->user()->partner;
        $apiClient = $clientFactory->make($partner);
        $reportData = $apiClient->getReportData(
            $this->getFromDateTime($this->getDefaultRange()),
            $this->getToDateTime()
        );

        $currency = $this->getCurrency().' ';
        $toolsData = collect($reportData->tools)->each(static function (ToolReportData $toolData) use ($currency) {
            $toolData->amountOfOrders = $currency.\HAmount::novaAmountFormat($toolData->amountOfOrders);
            $toolData->averageOrderValue = $currency.\HAmount::novaAmountFormat($toolData->averageOrderValue);

            return $toolData;
        });

        return response()->json($toolsData);
    }

    public function getCards(NovaRequest $request)
    {
        return collect($this->cards())
            ->filter
            ->authorize($request)
            ->values();
    }

    protected function cards(): array
    {
        return [
            app(ToolsCountValue::class),
            app(PartnerPurchasesAmountTrend::class),
            app(AverageChequeIncreaseValue::class)
                ->withMeta([
                    'addClass' => 'increased-value',
                ]),
            app(AveragePurchaseAmountTrend::class),
            app(PurchaseCountTrend::class),
            app(IssuedPromoCodesCountValue::class),
            app(UsedPromoCodesCountValue::class),
            app(UniqueUsersCountValue::class),
            app(SentEmailsCountTrend::class),
            app(ContactsTrend::class),
            app(SocialPostsTrend::class),
            SimpleTable::make(__('Results for each tool'))
                ->setHeaders([
                    __('BitRewards tool'),
                    __('Amount of orders'),
                    __('Average order value'),
                    __('Number of orders'),
                ])
                ->setDataUrl('dashboard/table/tools-statistic'),
        ];
    }
}
