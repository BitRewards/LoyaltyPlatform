<?php

namespace App\Services;

use App\DTO\ActionValue;
use App\DTO\ActionValueConstraint;
use App\DTO\ActionValuePolicyRule;
use App\Models\Action;

class ActionValueService
{
    /**
     * @var Action
     */
    protected $action;

    public function __construct(
        Action $action
    ) {
        $this->action = $action;
    }

    /**
     * @param $policy
     * @param int $sort
     *
     * @return array
     */
    protected function sortPolicyRules($policy, $sort = SORT_DESC)
    {
        if (!is_array($policy)) {
            return [];
        }

        $minAmountValues = array_map(function (ActionValuePolicyRule $rule) {
            return $rule->condition['minAmount'] ?? null;
        }, $policy);

        array_multisort($minAmountValues, $sort, $policy);

        return $policy;
    }

    /**
     * @param ActionValueConstraint $constraint
     * @param $value
     *
     * @return bool
     */
    protected function checkConstraint(ActionValueConstraint $constraint, $value)
    {
        switch ($constraint->type) {
            case 'minAmount':
                return (float) $value >= (float) $constraint->value;

                break;

            default:
                return true;

                break;
        }
    }

    /**
     * @param $condition
     * @param $value
     *
     * @return bool
     */
    protected function matchCondition($condition, $value)
    {
        if (!is_array($condition)) {
            return false;
        }

        foreach ($condition as $constraintType => $constraintValue) {
            if (!$this->checkConstraint(new ActionValueConstraint($constraintType, $constraintValue), $value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return ActionValue
     */
    public function getDefaultActionValue()
    {
        return new ActionValue((float) $this->action->value, $this->action->value_type);
    }

    /**
     * @param Action $action
     *
     * @return array|null
     */
    public function getConditionalRewardsData(Action $action)
    {
        if (!$action->hasValuePolicy()) {
            return null;
        }

        $sortedRules = $this->sortPolicyRules($action->getValuePolicy());
        $minAmount = null;
        $maxAmount = null;

        $rewards = array_map(function (\App\DTO\ActionValuePolicyRule $rule) use ($action, &$minAmount, &$maxAmount) {
            $childAction = clone $action;
            $childAction->config = array_merge($childAction->config, [Action::CONFIG_VALUE_POLICY => null]);
            $childAction->value = $rule->value;
            $childAction->value_type = $rule->valueType;

            $rewardData = \HAction::getActionAmountData($childAction);

            $minAmount = $rule->condition['minAmount'] ?? null;

            if (!$maxAmount && $minAmount) {
                $rewardData->orderConstraintString = __('above %amount%', [
                    'amount' => \HAmount::fSignBold($minAmount, $action->partner->currency),
                ]);
            } elseif ($maxAmount) {
                $rewardData->orderConstraintString = __('up to %amount%', [
                    'amount' => \HAmount::fSignBold($maxAmount, $action->partner->currency),
                ]);
            }
            $maxAmount = $minAmount;

            return $rewardData;
        }, $sortedRules);

        return array_reverse($rewards);
    }

    /**
     * @param float $sourceAmount
     *
     * @return ActionValue
     */
    public function getValueForSourceAmount(float $sourceAmount): ActionValue
    {
        if (!$this->action->hasValuePolicy()) {
            return $this->getDefaultActionValue();
        }

        $sortedRules = $this->sortPolicyRules($this->action->getValuePolicy());
        /*
         * @var ActionValuePolicyRule
         */
        while ($rule = array_shift($sortedRules)) {
            if ($this->matchCondition($rule->condition, $sourceAmount)) {
                return new ActionValue((float) ($rule->value), $rule->valueType);
            }
        }

        return $this->getDefaultActionValue();
    }
}
