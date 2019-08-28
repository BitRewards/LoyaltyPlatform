<?php

namespace App\DTO\PartnerPage;

use App\Models\Reward;

class RewardData
{
    public $id;

    public $type;

    public $valueType;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $shortDescription;

    /**
     * @var RewardViewData
     */
    public $viewData;

    /**
     * @var float
     */
    public $price;

    /**
     * @var string
     */
    public $priceType;

    public function isSpecialType(): bool
    {
        return Reward::TYPE_BITREWARDS_PAYOUT === $this->type;
    }

    public function getSpecialTypeTemplateName()
    {
        if (Reward::TYPE_BITREWARDS_PAYOUT === $this->type) {
            return 'bitrewards-payout';
        }

        throw new \RuntimeException('Unknown type');
    }

    public function isGiftdDiscountType(): bool
    {
        return Reward::TYPE_GIFTD_DISCOUNT === $this->type;
    }

    public function isFixedValueType()
    {
        return Reward::VALUE_TYPE_FIXED === $this->valueType;
    }

    public function isFiatPriceType()
    {
        return Reward::PRICE_TYPE_FIAT === $this->priceType;
    }

    public function isFiatWithdrawType(): bool
    {
        return Reward::TYPE_FIAT_WITHDRAW === $this->type;
    }
}
