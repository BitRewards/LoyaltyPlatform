<?php

namespace App\DTO\PartnerPage;

use App\Models\Partner;
use App\Models\User;
use App\Services\ActionProcessors\JoinFb;
use App\Services\ActionProcessors\JoinVk;
use App\Services\ActionProcessors\ShareableInterface;
use App\Services\ActionService;
use App\Services\ActionValueService;
use Illuminate\Routing\UrlGenerator;

class ActionDataFactory
{
    /**
     * @var ActionService
     */
    protected $actionService;

    /**
     * @var UrlGenerator
     */
    protected $urlGenerator;

    /**
     * @var \HAction
     */
    protected $actionHelper;

    /**
     * @var \HAmount
     */
    protected $amountHelper;

    public function __construct(
        ActionService $actionService,
        UrlGenerator $urlGenerator,
        \HAction $actionHelper,
        \HAmount $amountHelper
    ) {
        $this->actionService = $actionService;
        $this->urlGenerator = $urlGenerator;
        $this->actionHelper = $actionHelper;
        $this->amountHelper = $amountHelper;
    }

    public function factory(Partner $partner, User $user = null, array $tags = []): array
    {
        $actions = $this->actionService->getPartnerActions($partner, $user, $tags);
        $result = [];

        foreach ($actions as $action) {
            $actionData = new ActionData();
            $actionData->viewData = new ActionViewData();

            $actionData->id = $action->id;
            $actionData->type = $action->type;
            $actionData->valueType = $action->value_type;
            $actionData->viewData->canBeDone = $action->viewData['can_be_done'] ?? null;
            $actionData->viewData->impossibleReason = $action->viewData['impossible_reason'] ?? null;
            $actionData->viewData->iconClass = $this->actionHelper::getIconClass($action);
            $actionData->viewData->title = $this->actionHelper::getTitle($action);
            $actionData->viewData->description = $this->actionHelper::getDescription($action);

            if ($user) {
                $processor = $action->getActionProcessor();

                if ($processor instanceof ShareableInterface) {
                    $actionData->viewData->shareUrl = $processor->getUrl($user);
                }

                if ($processor instanceof JoinVk) {
                    $actionData->viewData->groupId = $processor->getGroupId();
                }

                if ($processor instanceof JoinFb) {
                    $actionData->viewData->pageUrl = $processor->getPageUrl();
                }

                $actionData->viewData->clientEventProcessUrl = $this
                    ->urlGenerator
                    ->route('client.events.process', [
                        'partner' => $partner->key,
                        'action' => $action->id,
                    ]);
            }

            $actionData->viewData->rewardAmount = \HAction::getBonusAmountString($action);

            if ($action->hasValuePolicy()) {
                $actionData->viewData->valuePolicyRules = app(ActionValueService::class)->getConditionalRewardsData($action);
            }
            $result[] = $actionData;
        }

        return $result;
    }
}
