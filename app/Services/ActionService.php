<?php

namespace App\Services;

use App\Models\Action;
use App\Models\StoreEvent;
use App\Models\Partner;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ActionService
{
    public function checkLimits(Action $action, User $user, StoreEvent $event)
    {
        $limitPerUser = $action->getActionProcessor()->getLimitPerUser();

        if ($limitPerUser) {
            $overallCount = Transaction::model()->whereAttributes([
                'user_id' => $user->id,
                'action_id' => $action->id,
                'status' => Transaction::STATUS_CONFIRMED,
            ])->count();

            if ($overallCount >= $limitPerUser) {
                return false;
            }
        }

        if ($minTimeBetween = $action->getActionProcessor()->getMinTimeBetween()) {
            $lastAction = Transaction::model()->whereAttributes([
                'user_id' => $user->id,
                'action_id' => $action->id,
                'status' => Transaction::STATUS_CONFIRMED,
            ])->orderBy('created', 'DESC')->first();

            $period = $event->created_at->getTimestamp() - $lastAction->created_at->getTimestamp();

            if ($period < $minTimeBetween) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param Partner    $partner
     * @param User|null  $user
     * @param array|null $tags
     *
     * @return Collection|Action[]
     */
    public function getPartnerActions(Partner $partner, User $user = null, array $tags = null): Collection
    {
        $query = Action
            ::model()
            ->whereAttributes([
                'partner_id' => $partner->id,
                'status' => Action::STATUS_ENABLED,
                'is_system' => false,
            ])
            ->with('specialOfferAction');

        if (!empty($tags)) {
            $query->whereIn('tag', $tags);
        }

        $actions = $query->get();

        if ($user) {
            return $this->prepareActions($partner, $actions, $user);
        }

        return $actions;
    }

    /**
     * @param Partner $partner
     *
     * @return Action|null
     */
    public function getRefillAction(Partner $partner)
    {
        return Action::model()
            ->whereAttributes([
                'partner_id' => $partner->id,
                'type' => Action::TYPE_REFILL_BIT,
            ])
            ->limit(1)
            ->first();
    }

    /**
     * @param Partner $partner
     *
     * @return Action|null
     */
    public function getExchangeEthToBitAction(Partner $partner)
    {
        return Action::model()
            ->whereAttributes([
                'partner_id' => $partner->id,
                'type' => Action::TYPE_EXCHANGE_ETH_TO_BIT,
            ])
            ->limit(1)
            ->first();
    }

    public function prepareActions(Partner $partner, Collection $actions, User $user): Collection
    {
        /**
         * @todo resolve cycle dependency problem
         */
        $transactionsData = app(TransactionService::class)->getUserTransactionDetails($user);

        /*
         * @var Action
         */
        foreach ($actions as $action) {
            if (isset($transactionsData[$action->id])) {
                $action->viewData['total_count'] = ($totalCount = $transactionsData[$action->id][0]->total_count);
                $action->viewData['max_created_at'] = $maxCreatedAt = new Carbon($transactionsData[$action->id][0]->max_created_at);
                $action->viewData['max_created_at_timestamp'] = $maxCreatedAt->getTimestamp();

                $canBeDone = true;
                $impossibleReason = null;
                $limitPerUser = $action->getActionProcessor()->getLimitPerUser();

                if ($limitPerUser && $totalCount >= $limitPerUser) {
                    $canBeDone = false;
                    $impossibleReason = __('Completed %s', \HDate::dateFull($maxCreatedAt));
                }

                $minTimeBetween = $action->getActionProcessor()->getMinTimeBetween();

                if ($minTimeBetween) {
                    $currentTimeBetween = time() - $maxCreatedAt->getTimestamp();

                    if ($currentTimeBetween < $minTimeBetween) {
                        $closestPossibleDate = $maxCreatedAt->addSeconds($minTimeBetween);
                        $canBeDone = false;
                        $impossibleReason = __('Wait until %s', \HDate::dateTimeFull($closestPossibleDate));
                    }
                }

                $action->viewData['can_be_done'] = $canBeDone;
                $action->viewData['impossible_reason'] = $impossibleReason;
            }
        }

        $sortBy = [
            Action::VALUE_TYPE_PERCENT => 1,
            Action::VALUE_TYPE_FIXED => 2,
        ];

        return $actions->sort(function (Action $a, Action $b) use ($sortBy) {
            $r = ($b->viewData['can_be_done'] ?? 1) - ($a->viewData['can_be_done'] ?? 1);

            if (0 !== $r) {
                return $r;
            }

            $r = ($b->viewData['max_created_at_timestamp'] ?? 0) - ($a->viewData['max_created_at_timestamp'] ?? 0);

            if (0 !== $r) {
                return $r;
            }

            $r = ($sortBy[$a->value_type] ?? 0) - ($sortBy[$b->value_type] ?? 0);

            if (0 !== $r) {
                return $r;
            }

            return $a->id - $b->id;
        })->filter(function (Action $action) use ($partner) {
            if (\HLanguage::LANGUAGE_RU !== $partner->default_language) {
                if (Action::TYPE_JOIN_VK === $action->type || Action::TYPE_SHARE_VK === $action->type) {
                    return false;
                }
            }

            return true;
        });
    }

    public function getReferralAction(Partner $partner): ?Action
    {
        return Action::where('type', '=', Action::TYPE_ORDER_REFERRAL)
            ->where('status', '=', Action::STATUS_ENABLED)
            ->where('partner_id', '=', $partner->id)
            ->first();
    }

    public function getSignupAction(Partner $partner): ?Action
    {
        return Action::where('type', '=', Action::TYPE_SIGNUP)
                     ->where('status', '=', Action::STATUS_ENABLED)
                     ->where('partner_id', '=', $partner->id)
                     ->first();
    }
}
