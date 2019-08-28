<?php

namespace App\Services;

use App\Common\Mailer;
use App\DTO\CredentialData;
use App\Models\Action;
use App\Models\Partner;
use App\Models\Reward;
use App\Models\StoreEntity;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory;
use Faker\Generator;

/**
 * Class FakeDataService.
 *
 * Provides methods for creating fake data to fill database with
 */
class FakeDataService
{
    /**
     * @var PartnerService
     */
    private $partnerService;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var Factory
     */
    private $fakerFactory;

    /**
     * @var Generator
     */
    private $faker;

    /**
     * @var User
     */
    private $lastUser;
    private $usedActions = [];
    private $availableActions;

    public function __construct(
        PartnerService $partnerService,
        UserService $userService,
        Factory $fakerFactory
    ) {
        $this->partnerService = $partnerService;
        $this->userService = $userService;
        $this->fakerFactory = $fakerFactory;
    }

    public function fake()
    {
        throw new \BadMethodCallException('Not Implemented');
    }

    /**
     * @return CredentialData
     */
    private function getFakeCredentialData(): CredentialData
    {
        $data = new CredentialData();
        $data->email = sprintf('giftd-crm-%s-%s@maildrop.cc', str_random(10), Mailer::DO_NOT_SEND_EMAIL_SUBSTRING_INDICATOR);
        $data->email_confirmed_at = Carbon::now();

        $data->name = $this->faker->name;

        return $data;
    }

    /**
     * @param Partner $partner
     *
     * @return User
     */
    public function createUserForPartner(Partner $partner): User
    {
        $this->faker = $this->fakerFactory::create(\HLanguage::getDefaultLocaleForLanguage($partner->default_language));

        return $this->userService->createClient($this->getFakeCredentialData(), $partner);
    }

    private function isSourceAmountInFiat(Action $action): bool
    {
        return StoreEntity::TYPE_ORDER === $action->getActionProcessor()->getEntityType()
            || StoreEntity::TYPE_AFFILIATE_ACTION === $action->getActionProcessor()->getEntityType();
    }

    private function getTransactionAmount(Action $action)
    {
        $sourceAmount = random_int(1000, 5000);

        if ($minAmount = $action->getActionProcessor()->getSourceEventMinAmount()) {
            if ($sourceAmount < $minAmount) {
                return 0;
            }
        }

        $isBitrewardsEnabled = $action->partner->isBitrewardsEnabled();

        $actionValue = app()
            ->make(ActionValueService::class, ['action' => $action])
            ->getValueForSourceAmount((float) $sourceAmount);

        switch ($actionValue->valueType) {
            case Action::VALUE_TYPE_FIXED:
                return $actionValue->value;

            case Action::VALUE_TYPE_PERCENT:
                if ($this->isSourceAmountInFiat($action)) {
                    if ($isBitrewardsEnabled) {
                        $amountInPoints = \HAmount::fiatToPoints($sourceAmount, $action->partner);
                    } else {
                        $amountInPoints = $sourceAmount * $action->partner->money_to_points_multiplier;
                    }
                } else {
                    $amountInPoints = $sourceAmount;
                }

                $amount = $amountInPoints * ($actionValue->value / 100);

                return $isBitrewardsEnabled ? $amount : floor($amount);

            case Action::VALUE_TYPE_FIXED_FIAT:
                $amountInPoints = \HAmount::fiatToPoints($actionValue->value, $action->partner);

                return $isBitrewardsEnabled ? $amountInPoints : floor($amountInPoints);

            default:
                throw new \BadMethodCallException('Not implemented');
        }
    }

    /**
     * @param User $user
     *
     * @throws \Exception
     */
    public function createActionTransactionForUser(User $user, $count = 1)
    {
        if ($this->lastUser !== $user) {
            $this->availableActions = null;
            $this->usedActions = [];
            $this->lastUser = $user;
        }

        if (!$this->availableActions) {
            $this->availableActions = $user->partner->actions()->where('is_system', '!=', true)->whereNotIn('type',
                [Action::TYPE_SIGNUP])->get();
        }

        $usedActions = &$this->usedActions;
        $availableActions = &$this->availableActions;

        while ($availableActions->count() && $count--) {
            /**
             * @var Action
             */
            $randomKey = $availableActions->keys()->random();
            $action = $availableActions->get($randomKey);

            $actionProcessor = $action->getActionProcessor();

            if (!isset($usedActions[$action->id])) {
                $usedActions[$action->id] = 0;
            }

            ++$usedActions[$action->id];

            if ($actionProcessor->getLimitPerUser() && !empty($usedActions[$action->id]) && $usedActions[$action->id] >= $actionProcessor->getLimitPerUser()) {
                $availableActions->forget($randomKey);
            }

            \DB::transaction(function () use ($action, $user) {
                $transaction = new Transaction();
                $transaction->type = Transaction::TYPE_ACTION;
                $transaction->action_id = $action->id;
                $transaction->comment = null;
                $transaction->partner_id = $user->partner_id;
                $transaction->user_id = $user->id;
                $transaction->balance_change = $this->getTransactionAmount($action);
                $transaction->status = Transaction::STATUS_CONFIRMED;

                if (!$transaction->save()) {
                    \HMisc::echoIfDebuggingInConsole('Failed to save transaction');
                } else {
                    \HMisc::echoIfDebuggingInConsole('Saved transaction: '.$transaction->balance_change.' #'.$transaction->id);
                }

                $oldBalance = $user->balance;

                $user = app(UserService::class)->recalculateBalance($user);

                $transaction->update([
                    'balance_before' => $oldBalance,
                    'balance_after' => $user->balance,
                ]);
            });
        }
    }

    /**
     * @param User $user
     * @param int  $count
     *
     * @throws \App\Exceptions\RewardAcquiringException
     */
    public function createRewardTransactionForUser(User $user, $count = 1)
    {
        $availableRewards = $user->partner->rewards()->where('status', 'enabled')->get();

        $successCount = 0;

        while ($availableRewards->count() && $count--) {
            /**
             * @var Reward
             */
            $randomKey = $availableRewards->keys()->random();
            $reward = $availableRewards->get($randomKey);

            try {
                \DB::transaction(function () use ($reward, $user) {
                    $rewardProcessor = $reward->getRewardProcessor();
                    $rewardProcessor->acquire($user);
                });

                ++$successCount;
            } catch (\Throwable $e) {
                \DB::rollBack();
            }
        }

        return $successCount;
    }

    public function createTransactionsForUser(User $user, $count = 1)
    {
        while ($count--) {
            if (mt_rand(0, 1)) {
                \HMisc::echoIfDebuggingInConsole('Action');
                $this->createActionTransactionForUser($user);
            } else {
                \HMisc::echoIfDebuggingInConsole('Reward');

                if (!$this->createRewardTransactionForUser($user)) {
                    $this->createActionTransactionForUser($user);
                }
            }
        }
    }
}
