<?php

namespace App\DTO\PartnerPage;

use App\Models\Action;

class ActionData
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $type;

    /**
     * @var ActionViewData
     */
    public $viewData;

    /**
     * @var string
     */
    public $valueType;

    /**
     * @var string
     */
    public $modalClass;

    public function isOrderReferralType(): bool
    {
        return Action::TYPE_ORDER_REFERRAL === $this->type;
    }

    public function isOrderCashBackType(): bool
    {
        return Action::TYPE_ORDER_CASHBACK === $this->type;
    }

    public function isJoinVKType(): bool
    {
        return Action::TYPE_JOIN_VK === $this->type;
    }

    public function isJoinFBType(): bool
    {
        return Action::TYPE_JOIN_FB === $this->type;
    }

    public function isShareFBType(): bool
    {
        return Action::TYPE_SHARE_FB === $this->type;
    }

    public function isShareVKType(): bool
    {
        return Action::TYPE_SHARE_VK === $this->type;
    }

    public function isShareInstagramType(): bool
    {
        return Action::TYPE_SHARE_INSTAGRAM === $this->type;
    }

    public function isSubscribeTelegramType(): bool
    {
        return Action::TYPE_SUBSCRIBE_TO_TELEGRAM === $this->type;
    }

    public function isCustomSocialActionType(): bool
    {
        return Action::TYPE_CUSTOM_SOCIAL_ACTION === $this->type;
    }
}
