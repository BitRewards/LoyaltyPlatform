<?php

namespace App\DTO\PartnerPage;

use App\Models\Partner;
use App\Models\User;
use App\Services\RewardService;
use Illuminate\Routing\UrlGenerator;

class RewardDataFactory
{
    /**
     * @var RewardService
     */
    protected $rewardService;

    /**
     * @var UrlGenerator
     */
    protected $urlGenerator;

    /**
     * @var \HReward
     */
    protected $rewardHelper;

    /**
     * @var \HAmount
     */
    protected $amountHelper;

    /**
     * @var \HAction
     */
    protected $actionHelper;

    /**
     * @var \HLanguage
     */
    protected $languageHelper;

    public function __construct(
        RewardService $rewardService,
        UrlGenerator $urlGenerator,
        \HReward $rewardHelper,
        \HAmount $amountHelper,
        \HAction $actionHelper,
        \HLanguage $languageHelper
    ) {
        $this->rewardService = $rewardService;
        $this->urlGenerator = $urlGenerator;
        $this->rewardHelper = $rewardHelper;
        $this->amountHelper = $amountHelper;
        $this->actionHelper = $actionHelper;
        $this->languageHelper = $languageHelper;
    }

    public function factory(Partner $partner, User $user = null, array $tags = []): array
    {
        $rewards = $this->rewardService->getPartnerRewardsForUser($partner, $user, $tags);
        $result = [];

        foreach ($rewards as $reward) {
            $rewardData = new RewardData();
            $rewardData->viewData = new RewardViewData();

            $rewardData->id = $reward->id;
            $rewardData->type = $reward->type;
            $rewardData->valueType = $reward->value_type;
            $rewardData->title = $this->rewardHelper::getTitle($reward);
            $rewardData->description = $reward->description;
            $rewardData->shortDescription = $reward->description_short;
            $rewardData->price = $reward->price;
            $rewardData->priceType = $reward->price_type;

            $rewardData->viewData->reward = $this->rewardHelper::getPriceStr($reward);
            $rewardData->viewData->rewardAmount = $this->rewardHelper::points($reward);
            $rewardData->viewData->rewardValue = $this->rewardHelper::getValueStr($reward);
            $rewardData->viewData->isBigRewardValue = mb_strlen(strip_tags($rewardData->viewData->rewardValue)) >= 10;
            $rewardData->viewData->minimalRewardAmount = $reward->getRewardProcessor()->getDiscountMinAmountTotal();

            if ($this->languageHelper::isRussian()) {
                $rewardData->viewData->rewardMessage = __('single purchase discount');
            } elseif ($rewardData->isFixedValueType()) {
                $rewardData->viewData->rewardMessage = __('single purchase discount');
            } else {
                $rewardData->viewData->rewardMessage = __('single purchase discount');
            }

            if ($rewardData->viewData->minimalRewardAmount) {
                if ($this->languageHelper::isRussian()) {
                    $rewardData->viewData->minimalRewardMessage = __(
                        'discount for purchases over %s',
                        $this->amountHelper::fSignBold($rewardData->viewData->minimalRewardAmount, $partner->currency)
                    );
                } elseif ($rewardData->isFixedValueType()) {
                    $rewardData->viewData->minimalRewardMessage = __(
                        'off for purchases over %s',
                        $this->amountHelper::fSignBold($rewardData->viewData->minimalRewardAmount, $partner->currency)
                    );
                } else {
                    $rewardData->viewData->minimalRewardMessage = __(
                        'discount for purchases over %s',
                        $this->amountHelper::fSignBold($rewardData->viewData->minimalRewardAmount, $partner->currency)
                    );
                }
            }

            if ($user) {
                if ($rewardData->description) {
                    $rewardData->viewData->rewardDiscountMessage = $rewardData->description;
                } else {
                    if ($rewardData->viewData->minimalRewardAmount) {
                        $rewardData->viewData->rewardDiscountMessage = __(
                            'Use %s discount for purchase over %s',
                            $this->actionHelper::getValueStr(
                                $reward->partner,
                                $reward->value,
                                $reward->value_type
                            ),
                            $this->amountHelper::fSignBold(
                                $rewardData->viewData->minimalRewardAmount,
                                $partner->currency
                            ));
                    } else {
                        $rewardData->viewData->rewardDiscountMessage = __(
                            'Use %s discount for any purchase',
                            $this->actionHelper::getValueStr(
                                $reward->partner,
                                $reward->value,
                                $reward->value_type
                            )
                        );
                    }
                }

                $rewardPoints = $this->rewardHelper::points($reward);

                if ($rewardPoints) {
                    $pointsLeft = (int) ($rewardPoints - $user->balance);
                    $rewardData->viewData->pointsLeft = $pointsLeft;
                    $rewardData->viewData->fiatLeft = $this->amountHelper::fSign($this->amountHelper::pointsToFiat($pointsLeft, $partner), $partner->currency);
                    $rewardData->viewData->progressInPercent = round(($user->balance / $rewardPoints) * 100);
                }
            }

            $rewardData->viewData->clientRewardAcquireUrl = $this
                ->urlGenerator
                ->route('client.reward.acquire', [
                    'partner' => $partner->key,
                    'reward' => $reward->id,
                ]);

            $result[] = $rewardData;
        }

        return $result;
    }
}
