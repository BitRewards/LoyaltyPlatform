<?php

namespace App\DTO\Factory;

use App\DTO\RewardData;
use App\Models\Reward;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Collection;

class RewardFactory
{
    /**
     * @var PartnerFactory
     */
    protected $partnerFactory;

    /**
     * @var \HReward
     */
    protected $rewardHelper;

    /**
     * @var UrlGenerator
     */
    protected $urlGenerator;

    /**
     * @var \HAmount
     */
    protected $amountHelper;

    /**
     * @var SpecialOfferRewardFactory
     */
    protected $specialOfferRewardFactory;

    public function __construct(
        \HReward $rewardHelper,
        PartnerFactory $partnerFactory,
        UrlGenerator $urlGenerator,
        \HAmount $amountHelper
    ) {
        $this->rewardHelper = $rewardHelper;
        $this->partnerFactory = $partnerFactory;
        $this->urlGenerator = $urlGenerator;
        $this->amountHelper = $amountHelper;
    }

    public function factory(Reward $reward): RewardData
    {
        $rewardData = new RewardData();
        $rewardData->id = $reward->id;
        $rewardData->type = $reward->type;
        $rewardData->title = $this->rewardHelper::normalizeText($this->rewardHelper::getTitle($reward));
        $rewardData->description = $reward->description;
        $rewardData->price = $this->rewardHelper::getPriceStr($reward);
        $rewardData->priceAmount = $reward->price;
        $rewardData->value = $this->rewardHelper::normalizeText($this->rewardHelper::getValueStr($reward));
        $rewardData->valueAmount = $reward->value;
        $rewardData->valueType = $reward->value_type;
        $rewardData->priceType = $reward->price_type;

        $rewardData->priceBitTokens = $this->amountHelper::fiatToPoints($reward->price, $reward->partner, true);
        $rewardData->priceBitTokensStr = \HAmount::shorten($rewardData->priceBitTokens);

        $iconUrl = $this->rewardHelper::getIconUrl($reward);
        $rewardData->image = $this->urlGenerator->to($iconUrl);

        $rewardData->partner = $this->partnerFactory->factory($reward->partner);

        if ($reward->relationLoaded('specialOfferReward') && $reward->specialOfferReward) {
            $rewardData->specialOfferReward = app(SpecialOfferRewardFactory::class)->factory($reward->specialOfferReward);
        }

        return $rewardData;
    }

    /**
     * @param Collection $collection
     *
     * @return Collection|Reward[]
     */
    public function factoryCollection(Collection $collection): Collection
    {
        return $collection->map(function (Reward $reward) {
            return $this->factory($reward);
        });
    }
}
