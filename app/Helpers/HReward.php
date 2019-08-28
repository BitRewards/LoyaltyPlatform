<?php

use App\Models\Reward;

class HReward
{
    public static function getPriceStr(Reward $reward)
    {
        $partner = $reward->partner;

        switch ($reward->price_type) {
            case Reward::PRICE_TYPE_POINTS:
                return $partner->isMergeBalancesEnabled() && $partner->isFiatReferralEnabled()
                    ? \HAmount::fSign(\HAmount::pointsToFiat($reward->price, $partner), $partner->currency)
                    : \HAmount::pointsWithPartner($reward->price, $partner);

            case Reward::PRICE_TYPE_FIAT:
                return HAmount::fSign($reward->price, $partner->currency ?? null);

            default:
                return $reward->price;
        }
    }

    public static function getPriceTypeStr(Reward $reward)
    {
        switch ($reward->price_type) {
            case Reward::PRICE_TYPE_POINTS:
                return __('Points');

            case Reward::PRICE_TYPE_FIAT:
                return __('Fiat currency');

            default:
                return '';
        }
    }

    public static function getValueStr(Reward $reward)
    {
        return $reward->title ?: HAction::getValueStr($reward->partner, $reward->value, $reward->value_type);
    }

    public static function getTitle(Reward $reward)
    {
        if ($reward->title) {
            return $reward->title;
        }

        switch ($reward->type) {
            case Reward::TYPE_GIFTD_DISCOUNT:
                return __('Discount %s', HReward::getValueStr($reward));

            case Reward::TYPE_BITREWARDS_PAYOUT:
                return __('BIT Withdrawal');

            case Reward::TYPE_FIAT_WITHDRAW:
                return __('Fiat Withdrawal');

            default:
                return '';
        }
    }

    public static function normalizeText(string $string): string
    {
        $string = str_replace(
            [
                \HAmount::ROUBLE_REGULAR,
                \HAmount::ROUBLE_BOLD,
            ],
            '₽',
            $string
        );

        return strip_tags($string);
    }

    public static function getTitleExhaustive(Reward $reward)
    {
        if ($reward->title) {
            $result = $reward->title;

            if ($reward->description_short) {
                $result .= ' ('.$reward->description_short.')';
            }

            return $result;
        }

        switch ($reward->type) {
            case Reward::TYPE_GIFTD_DISCOUNT:
                $result = __('Discount %s', HReward::getValueStr($reward));

                if ($minTotal = $reward->getRewardProcessor()->getDiscountMinAmountTotal()) {
                    $result .= ' ('.__('for purchases starting at %s', HAmount::fSign($minTotal, $reward->partner->currency)).')';
                }

                return $result;

            default:
                return '';
        }
    }

    public static function getAll()
    {
        $titles = [
            Reward::TYPE_GIFTD_DISCOUNT => __('Discount'),
            Reward::TYPE_BITREWARDS_PAYOUT => __('BIT Withdrawal'),
            Reward::TYPE_FIAT_WITHDRAW => __('Fiat Withdrawal'),
        ];

        return $titles;
    }

    public static function getStatusStr(Reward $reward)
    {
        $statuses = self::getAllStatuses();

        return $statuses[$reward->status] ?? $statuses[Reward::STATUS_DISABLED];
    }

    public static function getAllStatuses()
    {
        return [
            Reward::STATUS_ENABLED => __('Active'),
            Reward::STATUS_DISABLED => __('Inactive'),
        ];
    }

    public static function getAllValueTypes()
    {
        return [
            Reward::VALUE_TYPE_FIXED => __('Fixed'),
            Reward::VALUE_TYPE_PERCENT => __('Percent'),
        ];
    }

    public static function getAllPriceTypes()
    {
        return [
            Reward::PRICE_TYPE_POINTS => __('Points'),
            Reward::PRICE_TYPE_FIAT => __('Fiat currency'),
        ];
    }

    /**
     * Get all reward types.
     *
     * @return array
     */
    public static function types(): array
    {
        $all = self::getAll();

        return array_keys($all);
    }

    /**
     * Get available value types.
     *
     * @return array
     */
    public static function valueTypes(): array
    {
        return [Reward::VALUE_TYPE_FIXED, Reward::VALUE_TYPE_PERCENT];
    }

    public static function priceTypes(): array
    {
        return [Reward::PRICE_TYPE_POINTS, Reward::PRICE_TYPE_FIAT];
    }

    /**
     * Get available statuses.
     *
     * @return array
     */
    public static function statuses(): array
    {
        return [Reward::STATUS_ENABLED, Reward::STATUS_DISABLED];
    }

    public static function getIconClass(Reward $reward)
    {
        switch ($reward->type) {
            case Reward::TYPE_GIFTD_DISCOUNT:
                return 'spent';

            default:
                return 'spent';
        }
    }

    public static function getIconUrl(Reward $reward): string
    {
        return '/assets/icons/gift.svg';
    }

    public static function points(Reward $reward)
    {
        switch ($reward->price_type) {
            case Reward::PRICE_TYPE_POINTS:
                return $reward->price;

            case Reward::PRICE_TYPE_FIAT:
                return ceil(\HAmount::fiatToPoints($reward->price, $reward->partner));

            default:
                return $reward->price;
        }
    }
}
