<?php

namespace App\Services\ActionProcessors;

use App\DTO\StoreEventData;
use App\DTO\TransactionData;
use App\DTO\CredentialData;
use App\Exceptions\StoreEventProcessingException;
use App\Models\Action;
use App\Models\Code;
use App\Models\Partner;
use App\Models\StoreEntity;
use App\Models\StoreEvent;
use App\Models\Transaction;
use App\Models\TransactionOutput;
use App\Models\User;
use App\Services\ActionService;
use App\Services\ActionValueService;
use App\Services\EntityDataProcessors\AffiliateAction;
use App\Services\EntityDataProcessors\HasTargetActionId;
use App\Services\EntityDataProcessors\Order;
use App\Services\UserService;
use Carbon\Carbon;

abstract class Base
{
    private $action;
    private $config;

    protected $requiresEntityConfirmation = true;
    protected $requiresEntity = true;

    public function __construct(Action $action)
    {
        $this->action = $action;
        $this->config = $action->config ?: [];
    }

    public function getEntityType()
    {
        return null;
    }

    public function getEventAction()
    {
        return null;
    }

    public function handle(StoreEvent $event)
    {
        $this->process($event);
    }

    public function canHandle(StoreEvent $event)
    {
        if ($this->getEventAction() && $event->action != $this->getEventAction()) {
            return false;
        }

        if ($this->getEntityType() && $event->entity_type != $this->getEntityType()) {
            return false;
        }

        if ($event->getTargetActionId() && $event->getTargetActionId() != $this->getAction()->id) {
            return false;
        }

        if ($this->isEntityRequired()) {
            $entity = $event->entity;
            $dataProcessor = $entity->getDataProcessor();

            if ($dataProcessor instanceof HasTargetActionId) {
                $targetActionId = $dataProcessor->getTargetActionId();

                if (!$targetActionId || $targetActionId != $this->getAction()->id) {
                    return false;
                }
            }
        }

        return true;
    }

    public function getAction()
    {
        return $this->action;
    }

    protected function getSetting($key)
    {
        return $this->config[$key] ?? null;
    }

    protected function setSetting($key, $value)
    {
        $this->config[$key] = $value;
        $this->action->config = $this->config;
        $this->action->save();
    }

    public function isEntityConfirmationRequired()
    {
        return $this->requiresEntityConfirmation;
    }

    public function isEntityRequired()
    {
        return $this->requiresEntity;
    }

    /**
     * @param StoreEvent $event
     *
     * @return Transaction
     *
     * @throws StoreEventProcessingException
     */
    private function getExistingTransaction(StoreEvent $event)
    {
        $action = $this->getAction();

        if ($this->isEntityRequired()) {
            $entity = $event->entity;
            $transactions = Transaction::model()->whereAttributes([
                'source_store_entity_id' => $entity->id,
                'action_id' => $action->id,
            ])->get();
        } else {
            $transactions = Transaction::model()->whereAttributes([
                'source_store_event_id' => $event->id,
                'action_id' => $action->id,
            ])->get();
        }

        if (!count($transactions)) {
            return null;
        }

        if (count($transactions) > 1) {
            throw new StoreEventProcessingException("More then 1 transaction found for action {$action->id}, event {$event->id}");
        }

        return $transactions[0];
    }

    protected function getSourceEntityAmount(StoreEntity $entity): float
    {
        $processor = $entity->getDataProcessor();

        if ($processor instanceof Order) {
            return $processor->getAmountTotal() ?? 0.;
        }

        if ($processor instanceof AffiliateAction) {
            return $processor->getAffiliateRewardAmount();
        }

        return 0.;
    }

    private function isSourceAmountInFiat(): bool
    {
        return StoreEntity::TYPE_ORDER === $this->getEntityType()
            || StoreEntity::TYPE_AFFILIATE_ACTION === $this->getEntityType();
    }

    protected function getSourceEventAmount(StoreEvent $event)
    {
        return $event->data['amount'] ?? 0;
    }

    public function getSourceEventMinAmount()
    {
        return $this->getSetting(Action::CONFIG_SOURCE_ORDER_MIN_AMOUNT);
    }

    private function getSourceAmount(StoreEvent $event)
    {
        if ($this->isEntityRequired()) {
            $sourceAmount = $this->getSourceEntityAmount($event->entity);
        } else {
            $sourceAmount = $this->getSourceEventAmount($event);
        }

        return $sourceAmount;
    }

    private function getTransactionAmount(StoreEvent $event)
    {
        $sourceAmount = $this->getSourceAmount($event);

        if ($minAmount = $this->getSourceEventMinAmount()) {
            if ($sourceAmount < $minAmount) {
                return 0;
            }
        }

        $isBitrewardsEnabled = $this->action->partner->isBitrewardsEnabled();

        $actionValue = app()
            ->make(ActionValueService::class, ['action' => $this->action])
            ->getValueForSourceAmount((float) $sourceAmount);
//        $actionValue = $this->action->getValueForSourceAmount((float)$sourceAmount);

        switch ($actionValue->valueType) {
            case Action::VALUE_TYPE_FIXED:
                return $actionValue->value;

            case Action::VALUE_TYPE_PERCENT:
                if ($this->isSourceAmountInFiat()) {
                    $productValueOverrideSeen = false;
                    $entity = $event->entity;

                    if ($entity && StoreEntity::TYPE_ORDER == $entity->type) {
                        $amount = 0;

                        $orderLines = $entity->data->orderLines ?: [];

                        foreach ($orderLines as $orderLine) {
                            if ($isBitrewardsEnabled) {
                                $orderLineValueInPoints = \HAmount::fiatToPoints($orderLine['total_price'], $this->action->partner);
                            } else {
                                $orderLineValueInPoints = $orderLine['total_price'] * $this->action->partner->money_to_points_multiplier;
                            }

                            $valueOverride = $this->getValueOverrideForProduct($orderLine['product_id']);

                            if (isset($valueOverride)) {
                                $productValueOverrideSeen = true;
                                $amount += $orderLineValueInPoints * ($valueOverride / 100);
                            } else {
                                $amount += $orderLineValueInPoints * ($actionValue->value / 100);
                            }
                        }
                    }

                    if (!$productValueOverrideSeen) {
                        if ($isBitrewardsEnabled) {
                            $amountInPoints = \HAmount::fiatToPoints($sourceAmount, $this->action->partner);
                        } else {
                            $amountInPoints = $sourceAmount * $this->action->partner->money_to_points_multiplier;
                        }
                        $amount = $amountInPoints * ($actionValue->value / 100);
                    }
                } else {
                    $amountInPoints = $sourceAmount;
                    $amount = $amountInPoints * ($actionValue->value / 100);
                }

                return $isBitrewardsEnabled ? $amount : floor($amount);

            case Action::VALUE_TYPE_FIXED_FIAT:
                $amountInPoints = \HAmount::fiatToPoints($actionValue->value, $this->action->partner);

                return $isBitrewardsEnabled ? $amountInPoints : floor($amountInPoints);

            default:
                throw new StoreEventProcessingException("Unknown valueType returned from Action::getValueForSourceAmount for action #{$this->action->id}: {$actionValue->valueType}");
        }
    }

    protected function getTransactionReceiverUser(StoreEvent $event)
    {
        $user = $this->findReceiverUserBySimpleFields($event);

        $data = $this->isEntityRequired() ? $event->entity->data : $event->data;

        if (!$user) {
            if ($data->managerComment || $data->comment) {
                $comments = $data->managerComment."\n\n\n".$data->comment;
                $partnerId = $event->partner_id;

                if (preg_match_all("/$partnerId(\-|–|—)(\d+)/iu", $comments, $matches)) {
                    foreach ($matches[2] as $possibleUserId) {
                        $user = User::model()->whereAttributes([
                            'id' => (int) $possibleUserId,
                            'partner_id' => $partnerId,
                        ])->first();

                        if ($user) {
                            \Log::debug(sprintf('[ActionProcessors\\Base] Trying %s .. found = %s!', $possibleUserId, $user->id));

                            break;
                        }
                        \Log::debug(sprintf('[ActionProcessors\\Base] Trying %s .. not found', $possibleUserId));
                    }
                }

                if (!$user) {
                    if (preg_match_all("/\d[\d\s\-]{6,}\d/", $comments, $numericSequences)) {
                        // \Log::debug(sprintf("[ActionProcessors\\Base] Catching possibleCode in comments (partnerId: %s): %s\n", $partnerId, $comments));
                        foreach ($numericSequences[0] as $possibleCode) {
                            if ($code = Code::model()->findByPartnerAndToken($event->partner, $possibleCode)) {
                                if ($code->user_id) {
                                    $user = $code->user;
                                    \Log::debug(sprintf('[ActionProcessors\\Base] Trying %s .. found = %s!', $possibleCode, $user->id));

                                    break;
                                }
                            }
                            \Log::debug(sprintf('[ActionProcessors\\Base] Trying %s .. not found', $possibleCode));
                        }
                    }
                }
            }
        }

        if (!$user) {
            if ($data->promoCodes) {
                $promoCodes = (array) $data->promoCodes;
                $partner = $event->partner;

                foreach ($promoCodes as $promoCode) {
                    $transaction = Transaction::model()->findByPromoCode($partner, $promoCode);

                    if ($transaction) {
                        // this user acquired this promo code, so he's the owner of the order
                        $user = $transaction->user;

                        break;
                    }
                }
            }
        }

        return $user;
    }

    private function findReceiverUserBySimpleFields(StoreEvent $event): ?User
    {
        $data = $this->isEntityRequired() ? $event->entity->data : $event->data;
        \HMisc::echoIfDebuggingInConsole('Finding receiver user by simple fields for event '.$event->id.', data = '.\HJson::encode($event->data));

        if (!($data->email || $data->phone || $data->userCrmKey)) {
            return null;
        }

        $user = null;

        if ($data->userCrmKey) {
            $user = User::model()->findByKey($data->userCrmKey);
        }

        if ($user) {
            return $user;
        }

        $userFoundByEmail = $data->email ? User::model()->findByPartnerAndEmail($event->partner, $data->email) : null;
        $userFoundByPhone = $data->phone ? User::model()->findByPartnerAndPhone($event->partner, $data->phone) : null;

        \HMisc::echoIfDebuggingInConsole(
            "Search result for existing users by simple fields: email {$data->email} = ".
            ($userFoundByEmail ? $userFoundByEmail->id : 'not found').
            ", phone {$data->phone}  = ".
            ($userFoundByPhone ? $userFoundByPhone->id : 'not found')
        );

        switch ($event->partner->getAuthMethod()) {
            case Partner::AUTH_METHOD_EMAIL:
                $user = $userFoundByEmail;

                break;

            case Partner::AUTH_METHOD_PHONE:
                $user = $userFoundByPhone;

                break;

            default:
                break;
        }

        if ($user) {
            return $user;
        }

        return $userFoundByPhone ?: $userFoundByEmail;
    }

    private function autoCreateUserIfNeeded(StoreEvent $event)
    {
        if (!$event->partner->getSetting(Partner::SETTINGS_AUTO_SIGNUP_USERS_FROM_ORDERS)) {
            return;
        }

        $user = $this->findReceiverUserBySimpleFields($event);

        if (!$user) {
            $data = $this->isEntityRequired() ? $event->entity->data : $event->data;

            if ($data->email || $data->phone) {
                // auto creating now
                $user = app(UserService::class)->createClient(CredentialData::make([
                    'email' => $data->email,
                    'phone' => $data->phone,
                    'name' => $data->name,
                    'email_confirmed_at' => $data->email ? Carbon::now() : null,
                    'signup_type' => User::SIGNUP_TYPE_ORDER,
                    'referrer_key' => $data->refUserCrmKey ?? null,
                ]), $event->partner);
            }
        }
    }

    private function process(StoreEvent $event)
    {
        $this->autoCreateUserIfNeeded($event);

        $user = $this->getTransactionReceiverUser($event);
        $transaction = $this->getExistingTransaction($event);

        if (!$user) {
            if ($transaction) {
                $user = $transaction->user;

                if (!$user) {
                    throw new StoreEventProcessingException("Unable to find user while transaction exists, event id = {$event->id}");
                }
            } else {
                return;
            }
        }

        if ($transaction && $transaction->isBitrewardsPayout()) {
            $balanceChange = $transaction->balance_change;
            $oldBalance = $user->balance - $transaction->balance_change;
        } else {
            $balanceChange = $this->getTransactionAmount($event);

            if (!$balanceChange) {
                return;
            }

            if (!app(ActionService::class)->checkLimits($this->action, $user, $event)) {
                return;
            }

            $oldBalance = $user->balance;
        }

        $oldUserToRecalculateBalance = null;

        if (!$transaction) {
            $transaction = new Transaction();
            $transaction->type = Transaction::TYPE_ACTION;
            $transaction->action_id = $this->action->id;
            $transaction->comment = null;
            $transaction->partner_id = $event->partner_id;
            $transaction->actor_id = $this->findActorId($event);
            $transaction->source_store_event_id = $event->id;
            $transaction->source_store_entity_id = $event->store_entity_id;
            $transaction->user_id = $user->id;
            $transaction->data = $this->createTransactionData($event, $transaction->data);
        } else {
            if ($user) {
                if ($transaction->user_id != $user->id) {
                    // транзакция была отредактирована, и у нее сменился получатель - например, был изменен тех. комментарий к заказу (строка вида 24-51515, которую менеджер пишет в коммент к заказу)
                    // в таком случае мы "перевесим" транзакцию на нового юзера и пересчитаем баланс у старого юзера
                    // но если есть transaction_outputs - мы не будем ничего "перевешивать" во избежание путаницы - в дальнейшем с этим кейсом мб тоже разберемся
                    $transactionOutputsExist = TransactionOutput::whereRaw('transaction_from_id = ? OR transaction_to_id = ?', [$transaction->id, $transaction->id])->count();

                    if (!$transactionOutputsExist) {
                        // перевешиваем транзакцию на нового юзера
                        $transaction->user_id = $user->id;

                        // в конце функции мы пересчитаем у старого юзера баланс
                        $oldUserToRecalculateBalance = $transaction->user;
                    }
                }
            }
        }

        $transaction->balance_change = $balanceChange;

        if ($this->isEntityConfirmationRequired() && 0 != $transaction->balance_change) {
            $entity = $event->entity;

            if ($entity->rejected_at) {
                $transaction->status = Transaction::STATUS_REJECTED;
            } elseif ($entity->confirmed_at) {
                $transaction->status = Transaction::STATUS_CONFIRMED;
            } else {
                $transaction->status = Transaction::STATUS_PENDING;
            }
        } else {
            $transaction->status = Transaction::STATUS_CONFIRMED;
        }

        $transaction->save();

        $eventData = $event->data->toArray();

        if (!empty($eventData[StoreEventData::DATA_KEY_PARENT_TRANSACTION_ID])) {
            $parentTransaction = Transaction::model()->whereAttributes([
                'id' => $eventData[StoreEventData::DATA_KEY_PARENT_TRANSACTION_ID],
            ])->first();

            if ($parentTransaction) {
                $parentTransaction->data = array_replace_recursive($parentTransaction->data->toArray(), [
                    Transaction::DATA_CHILD_REFILL_BIT_TRANSACTION_ID => $transaction->id,
                ]);

                $parentTransaction->save();
            }
        }

        $user = app(UserService::class)->recalculateBalance($user);

        if ($oldUserToRecalculateBalance) {
            app(UserService::class)->recalculateBalance($oldUserToRecalculateBalance, true);
        }

        $transaction->update([
            'balance_before' => $oldBalance,
            'balance_after' => $user->balance,
        ]);
    }

    /**
     * @param StoreEvent      $event
     * @param TransactionData $currentData
     *
     * @return array
     */
    public function createTransactionData(StoreEvent $event, TransactionData $currentData = null)
    {
        return $currentData->toArray();
    }

    public function getLimitPerUser()
    {
        return $this->action->limit_per_user;
    }

    public function getMinTimeBetween()
    {
        return $this->action->limit_min_time_between;
    }

    /**
     * Find Actor ID in StoreEvent or get current user's ID.
     *
     * @param StoreEvent $event
     *
     * @return int
     */
    protected function findActorId(StoreEvent $event): int
    {
        $actorId = 0;

        if (0 !== $event->actor_id) {
            $actorId = $event->actor_id;
        } elseif (\Auth::check()) {
            $actorId = \Auth::user()->id;
        }

        return $actorId;
    }

    private function getValueOverrideForProduct($productId): ?float
    {
        $result = $this->action->config[Action::CONFIG_VALUE_OVERRIDE_FOR_PRODUCTS][$productId] ?? null;

        if (isset($result)) {
            return (float) $result;
        } else {
            return null;
        }
    }
}
