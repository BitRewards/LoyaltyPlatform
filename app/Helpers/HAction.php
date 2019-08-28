<?php

use App\DTO\PercentageActionValueData;
use App\Models\Action;
use App\Models\Partner;
use App\Services\ActionProcessors\OrderReferral;
use App\DTO\ActionRewardAmountData;

class HAction
{
    public static function getValueStr(Partner $partner, $value, $valueType, $useCurrencySign = true)
    {
        switch ($valueType) {
            case Action::VALUE_TYPE_PERCENT:
                return HAmount::percentage($value, false);

            case Action::VALUE_TYPE_FIXED:
                return $useCurrencySign ? HAmount::fSign($value, $partner->currency) : HAmount::fShort($value, $partner->currency);

            case Action::VALUE_TYPE_FIXED_FIAT:
                return HAmount::fSign($value, $partner->currency);

            default:
                return $value;
        }
    }

    public static function getRewardStr(Action $action)
    {
        $valueType = $action->value_type;
        $value = $action->value;
        $partner = $action->partner;

        switch ($valueType) {
            case Action::VALUE_TYPE_PERCENT:
                return HAmount::percentage($value, false);

            case Action::VALUE_TYPE_FIXED:
                return $partner->isMergeBalancesEnabled() && $partner->isFiatReferralEnabled()
                    ? \HAmount::fSignBold(\HAmount::pointsToFiat($value, $partner), $partner->currency)
                    : \HAmount::points($value, $partner);

            case Action::VALUE_TYPE_FIXED_FIAT:
                $partner = $action->partner ?? null;

                return HAmount::fSign($value, $partner->currency ?? null);

            default:
                return $value;
        }
    }

    public static function getStatusStr(Action $action)
    {
        $statuses = self::getAllStatuses();

        return $statuses[$action->status] ?? $statuses[Action::STATUS_DISABLED];
    }

    public static function getReferralRewardStr(OrderReferral $processor, $useCurrencySign = true)
    {
        $value = $processor->getReferralRewardValue();

        if (!$value) {
            return null;
        }

        return self::getValueStr($processor->getAction()->partner, $value, $processor->getReferralRewardValueType(), $useCurrencySign);
    }

    public static function getIconClass(Action $action)
    {
        switch ($action->type) {
            case Action::TYPE_ORDER_REFERRAL:
                return 'friend-buy';

            case Action::TYPE_ORDER_CASHBACK:
                return 'buy';

            case Action::TYPE_SIGNUP:
                return 'sign-up';

            case Action::TYPE_JOIN_FB:
                return 'join-fb';

            case Action::TYPE_JOIN_VK:
                return 'join-vk';

            case Action::TYPE_SHARE_FB:
                return 'share-fb';

            case Action::TYPE_SHARE_VK:
                return 'share-vk';

            case Action::TYPE_CUSTOM_BONUS:
                return 'gift';

            case Action::TYPE_SHARE_INSTAGRAM:
                return 'share-instagram';

            case Action::TYPE_SUBSCRIBE_TO_TELEGRAM:
                return 'subscribe-telegram';

            case Action::TYPE_CUSTOM_SOCIAL_ACTION:
                return 'custom-social';

            default:
                return 'gift';
        }
    }

    public static function getIconUrl(Action $action): string
    {
        $image = [
            Action::TYPE_ORDER_REFERRAL => 'friend_purchase',
            Action::TYPE_ORDER_CASHBACK => 'purchase',
            Action::TYPE_SIGNUP => 'key',
            Action::TYPE_JOIN_FB => 'fb',
            Action::TYPE_JOIN_VK => 'vk',
            Action::TYPE_SHARE_FB => 'post_fb',
            Action::TYPE_SHARE_VK => 'post_vk',
        ][$action->type] ?? 'gift';

        return "/assets/icons/{$image}.svg";
    }

    /**
     * @param \App\Models\Action|\stdClass $action
     *
     * @return mixed
     */
    public static function getTitle($action)
    {
        if ($action->title) {
            return $action->title;
        }

        return self::getTitleByType($action->type);
    }

    public static function getTitleText(Action $action): string
    {
        $title = str_replace(
            [
                \HAmount::ROUBLE_REGULAR,
                \HAmount::ROUBLE_BOLD,
            ],
            '₽',
            self::getTitle($action)
        );

        return strip_tags($title);
    }

    public static function getTitleByType($type)
    {
        switch ($type) {
            case Action::TYPE_SIGNUP:
                return __('Registration');

            case Action::TYPE_ORDER_CASHBACK:
                return __('For your purchases');

            case Action::TYPE_ORDER_REFERRAL:
                return __('For purchases of your friends');

            case Action::TYPE_CUSTOM_BONUS:
                return __('Bonus');

            case Action::TYPE_JOIN_FB:
                return __('Like on Facebook');

            case Action::TYPE_JOIN_VK:
                return __('Subscribe on VK');

            case Action::TYPE_SHARE_FB:
                return __('Share on Facebook');

            case Action::TYPE_SHARE_VK:
                return __('Share on VK');

            case Action::TYPE_REFILL_BIT:
                return __('Replenishment of BIT');

            case Action::TYPE_EXCHANGE_ETH_TO_BIT:
                return __('Exchange ETH to BIT');

            case Action::TYPE_AFFILIATE_ACTION_ADMITAD:
                return __('Affiliate Action (Admitad)');

            case Action::TYPE_SHARE_INSTAGRAM:
                return __('Share on Instagram');

            case Action::TYPE_SUBSCRIBE_TO_TELEGRAM:
                return __('Subscribe to Telegram');

            case Action::TYPE_CUSTOM_SOCIAL_ACTION:
                return __('Custom social action');

            default:
                return __('Bonus');
        }
    }

    public static function getAll()
    {
        $types = static::getAllTypes();

        $titles = [];

        foreach ($types as $type) {
            $titles[$type] = self::getTitleByType($type);
        }

        return $titles;
    }

    /**
     * Get all Action types.
     *
     * @return array
     */
    public static function getAllTypes(): array
    {
        return [
            Action::TYPE_SIGNUP,
            Action::TYPE_ORDER_CASHBACK,
            Action::TYPE_ORDER_REFERRAL,
            Action::TYPE_CUSTOM_BONUS,
            Action::TYPE_JOIN_FB,
            Action::TYPE_JOIN_VK,
            Action::TYPE_SHARE_FB,
            Action::TYPE_SHARE_VK,
            Action::TYPE_REFILL_BIT,
            Action::TYPE_EXCHANGE_ETH_TO_BIT,
            Action::TYPE_AFFILIATE_ACTION_ADMITAD,
            Action::TYPE_SHARE_INSTAGRAM,
            Action::TYPE_SUBSCRIBE_TO_TELEGRAM,
            Action::TYPE_CUSTOM_SOCIAL_ACTION,
        ];
    }

    public static function getAllStatuses()
    {
        return [
            Action::STATUS_ENABLED => __('Active'),
            Action::STATUS_DISABLED => __('Inactive'),
        ];
    }

    public static function getAllValueTypes()
    {
        return [
            Action::VALUE_TYPE_FIXED => __('Fixed'),
            Action::VALUE_TYPE_PERCENT => __('Percent'),
            Action::VALUE_TYPE_FIXED_FIAT => __('Fixed in foreign currency'),
        ];
    }

    /**
     * Get possible value types.
     *
     * @return array
     */
    public static function valueTypes(): array
    {
        return [Action::VALUE_TYPE_FIXED, Action::VALUE_TYPE_PERCENT, Action::VALUE_TYPE_FIXED_FIAT];
    }

    public static function getDescription(Action $action)
    {
        if ($action->description) {
            return $action->description;
        }

        switch ($action->type) {
            case Action::TYPE_SIGNUP:
                return __('Simply register in the rewards program');

            case Action::TYPE_ORDER_CASHBACK:
                return __('Complete the purchase in our store to get the bonus');

            case Action::TYPE_ORDER_REFERRAL:
                return __('Get bonus for purchases of your friends in our store');

            case Action::TYPE_SHARE_VK:
            case Action::TYPE_SHARE_FB:
                return __('Share our discount with your friends to get a reward');

            case Action::TYPE_SHARE_INSTAGRAM:
                return __('Share us to your instagram to obtain the reward');

            case Action::TYPE_SUBSCRIBE_TO_TELEGRAM:
                return __('Subscribe to our telegram channel to obtain the reward');

            case Action::TYPE_CUSTOM_SOCIAL_ACTION:
                return __('The default description of the custom social media action');

            default:
                return '';
        }
    }

    /**
     * @param Action $action
     *
     * @return PercentageActionValueData
     */
    public static function getPercentageActionValueData(Action $action, $amountInPoints = null)
    {
        $percent = $action->value / 100;

        $amount = 1;

        $isBitrewardsEnabled = $action->partner->isBitrewardsEnabled();
        $points = $percent * (
            $isBitrewardsEnabled
                ? \HAmount::fiatToPoints($amount, $action->partner)
                : ($amountInPoints ?? ($amount * $action->partner->money_to_points_multiplier)
            )
        );

        $minAmount = 100;

        while ($points != (int) $points && $amount < $minAmount) {
            $amount *= 10;
            $points *= 10;
        }

        return PercentageActionValueData::make([
            'points' => $points,
            'amount' => $amount,
        ]);
    }

    /**
     * @param Action $action
     *
     * @return \App\DTO\FixedActionValueData
     */
    public static function getFixedActionValueData(Action $action)
    {
        $amount = 1;

        return \App\DTO\FixedActionValueData::make([
            'points' => $action->value,
            'amount' => $amount * $action->partner->money_to_points_multiplier,
        ]);
    }

    public static function getFiatPointsActionValueData(Action $action, $amount = null)
    {
        $partner = $action->partner;

        return \App\DTO\FixedActionValueData::make([
            'points' => floor(\HAmount::fiatToPoints($amount ?? $action->value, $partner)),
            'amount' => $amount ?? $action->value,
        ]);
    }

    /**
     * @param Action $action
     *
     * @return ActionRewardAmountData
     */
    public static function getActionAmountData(Action $action): ActionRewardAmountData
    {
        $rewardData = new ActionRewardAmountData();

        $partner = $action->partner;

        if (!$action->hasValuePolicy()) {
            if (Action::VALUE_TYPE_FIXED === $action->value_type) {
                $rewardData->amount = $action->value;
                $rewardData->amountString = self::getRewardStr($action);
            } elseif (Action::VALUE_TYPE_PERCENT === $action->value_type) {
                $data = \HAction::getPercentageActionValueData($action);

                $rewardData->amount = $data->points;
                $rewardData->amountString = __(
                    '%reward% for every %amount%',
                    [
                        'reward' => $partner->isMergeBalancesEnabled() && $partner->isFiatReferralEnabled()
                            ? \HAmount::fSignBold(\HAmount::pointsToFiat($data->points, $partner), $partner->currency)
                            : \HAmount::points($data->points),
                        'amount' => \HAmount::fSignBold($data->amount, $action->partner->currency),
                    ]
                );
            } elseif (Action::VALUE_TYPE_FIXED_FIAT === $action->value_type) {
                $data = HAction::getFiatPointsActionValueData($action);

                $rewardData->amount = $data->points;
                $rewardData->amountString = HAmount::fSignBold($data->points, $action->partner->currency);
            } else {
                $rewardData->amount = 0;
                $rewardData->amountString = HAmount::points(0);
            }
        } else {
            /**
             * @var ActionRewardAmountData[]
             */
            $rewards = app(\App\Services\ActionValueService::class)->getConditionalRewardsData($action);

            $amounts = array_map(function (ActionRewardAmountData $item) {
                return $item->amount;
            }, $rewards);

            array_multisort($amounts, SORT_DESC, $rewards);
            $rewardData = array_shift($rewards);

            $rewardData->amountString = __('Up to ').$rewardData->amountString;
        }

        return $rewardData;
    }

    /**
     * @param Action $action
     *
     * @return string
     */
    public static function getBonusAmountString(Action $action)
    {
        if ($action->partner->isGradedPercentRewardModeEnabled()) {
            return __('-%s discount', \HAmount::pointsToPercentFormatted($action->value, false));
        }

        $rewardAmountData = static::getActionAmountData($action);

        return "+{$rewardAmountData['amountString']}";
    }
}
