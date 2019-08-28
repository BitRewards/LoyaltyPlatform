<?php

namespace App\DTO\Factory;

use App\DTO\ActionData;
use App\DTO\DTO;
use App\Generators\AffiliateUrlGenerator;
use App\Models\Action;
use App\Models\PersonInterface;
use App\Services\ActionProcessors\AffiliateActionAdmitad;
use App\Services\ActionProcessors\OrderCashback;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Collection;

class ActionFactory extends DTO
{
    /**
     * @var \HAction
     */
    protected $actionHelper;

    /**
     * @var PartnerFactory
     */
    protected $partnerFactory;

    /**
     * @var UrlGenerator
     */
    protected $urlGenerator;

    /**
     * @var AffiliateUrlGenerator
     */
    private $affiliateUrlGenerator;

    public function __construct(
        \HAction $actionHelper,
        PartnerFactory $partnerFactory,
        UrlGenerator $urlGenerator,
        AffiliateUrlGenerator $affiliateUrlGenerator
    ) {
        $this->actionHelper = $actionHelper;
        $this->partnerFactory = $partnerFactory;
        $this->urlGenerator = $urlGenerator;
        $this->affiliateUrlGenerator = $affiliateUrlGenerator;
    }

    public function factory(Action $action, PersonInterface $currentPerson = null): ActionData
    {
        $actionData = new ActionData();

        $actionData->id = $action->id;
        $actionData->title = $this->actionHelper::getTitleText($action);
        $actionData->description = $this->actionHelper::getDescription($action);

        $iconUrl = $this->actionHelper::getIconUrl($action);
        $actionData->image = $this->urlGenerator->to($iconUrl);

        $actionData->actionReward = $this->actionHelper::getRewardStr($action);
        $actionData->partner = $this->partnerFactory->factory($action->partner);

        $actionProcessor = $action->getActionProcessor();

        if ($actionProcessor instanceof OrderCashback) {
            $actionData->merchantUrl = $action->partner->url;
        }

        if ($actionProcessor instanceof AffiliateActionAdmitad) {
            if ($currentPerson) {
                $actionData->affiliateUrl = $this->affiliateUrlGenerator->generate($action, $currentPerson);
            }
        }

        return $actionData;
    }

    /**
     * @param Collection $collection
     *
     * @return Collection|Action[]
     */
    public function factoryCollection(Collection $collection): Collection
    {
        return $collection->map(function (Action $action) {
            return $this->factory($action);
        });
    }
}
