<?php

namespace App\Transformers;

use App\DTO\Factory\SpecialOfferActionFactory;
use App\Generators\AffiliateUrlGenerator;
use App\Models\PersonInterface;
use App\Models\User;
use App\Services\ActionProcessors\AffiliateActionAdmitad;
use App\Services\ActionProcessors\JoinFb;
use App\Services\ActionProcessors\JoinVk;
use App\Services\ActionProcessors\OrderCashback;
use App\Services\ActionProcessors\ShareableInterface;
use App\Services\ActionValueService;
use HAction;
use App\Models\Action;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class ActionTransformer extends TransformerAbstract
{
    /**
     * @var AffiliateUrlGenerator
     */
    private $affiliateUrlGenerator;

    /**
     * @var
     */
    private $currentPerson;

    /**
     * @var SpecialOfferActionFactory
     */
    private $specialOfferActionFactory;

    public function __construct(
        SpecialOfferActionFactory $specialOfferActionFactory,
        AffiliateUrlGenerator $affiliateUrlGenerator = null,
        PersonInterface $currentPerson = null
    ) {
        $this->affiliateUrlGenerator = $affiliateUrlGenerator;
        $this->currentPerson = $currentPerson;
        $this->specialOfferActionFactory = $specialOfferActionFactory;
    }

    public function transform(Action $action)
    {
        /** @var User $user */
        $user = Auth::user();
        $actionProcessor = $action->getActionProcessor();

        $result = [
            'id' => (int) $action->id,
            'type' => $action->type,
            'value' => HAction::getValueStr($action->partner, $action->value, $action->value_type, false),
            'raw_value' => $action->value,
            'value_type' => $action->value_type,
            'title' => HAction::getTitle($action),
            'description' => HAction::getDescription($action),
            'config' => $action->config,
            'partner_id' => (int) $action->partner_id,
            'status' => HAction::getStatusStr($action),
            'is_system' => $action->is_system ? 1 : 0,
            'viewData' => ($action->viewData ?? []) + [
                'iconClass' => HAction::getIconClass($action),
                'bonusAmount' => HAction::getBonusAmountString($action),
            ],
            'partnerLogoPicture' => \HCustomizations::logoPicture($action->partner),
        ];

        if ($action->hasValuePolicy()) {
            $result['viewData']['valuePolicyRules'] = app(ActionValueService::class)->getConditionalRewardsData($action);
        }

        if ($action->relationLoaded('specialOfferAction') && $action->specialOfferAction) {
            $result['specialOfferAction'] = $this->specialOfferActionFactory->factory($action->specialOfferAction)->toArray();
        }

        if ($actionProcessor instanceof JoinVk) {
            $result['viewData']['groupId'] = $actionProcessor->getGroupId();
        }

        if ($user && $actionProcessor instanceof ShareableInterface) {
            $result['viewData']['userUrl'] = $actionProcessor->getUrl($user);
        }

        if ($actionProcessor instanceof JoinFb) {
            $result['viewData']['pageUrl'] = $actionProcessor->getPageUrl();
        }

        if ($actionProcessor instanceof OrderCashback) {
            $result['viewData']['merchantUrl'] = $action->partner->url;
        }

        if ($actionProcessor instanceof AffiliateActionAdmitad) {
            $result['viewData']['affiliateUrl'] = $this->affiliateUrlGenerator->generate($action, $user);
        }

        return $result;
    }
}
