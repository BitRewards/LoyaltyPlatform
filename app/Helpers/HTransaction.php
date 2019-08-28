<?php

use App\Models\Transaction;

class HTransaction
{
    public static function getIconClass(Transaction $transaction)
    {
        if ($action = $transaction->action) {
            return HAction::getIconClass($action);
        } elseif ($reward = $transaction->reward) {
            return HReward::getIconClass($reward);
        } else {
            return 'buy';
        }
    }

    public static function getTitle(Transaction $transaction)
    {
        if ($action = $transaction->action) {
            return HAction::getTitle($action);
        } elseif ($reward = $transaction->reward) {
            return HReward::getTitle($reward);
        } else {
            return __('Transaction');
        }
    }

    public static function getTitleText(Transaction $transaction): string
    {
        $title = str_replace(
            [
                \HAmount::ROUBLE_REGULAR,
                \HAmount::ROUBLE_BOLD,
            ],
            '₽',
            self::getTitle($transaction)
        );

        return strip_tags($title);
    }

    public static function getTitleExhaustive(Transaction $transaction)
    {
        if ($action = $transaction->action) {
            return HAction::getTitle($action);
        } elseif ($reward = $transaction->reward) {
            return HReward::getTitleExhaustive($reward);
        } else {
            return __('Transaction');
        }
    }

    public static function getStatusIconClass(Transaction $transaction)
    {
        switch ($transaction->status) {
            case Transaction::STATUS_PENDING:
                return 'checked';

            case Transaction::STATUS_CONFIRMED:
                return 'accepted';

            case Transaction::STATUS_REJECTED:
                return 'rejected';

            default:
                return 'checked';
        }
    }

    public static function getStatusStr(Transaction $transaction)
    {
        switch ($transaction->status) {
            case Transaction::STATUS_PENDING:
                return
                    $transaction->getAutoConfirmationDatetime() ?
                        __('Will be confirmed on %s', HDate::dateTimeStrFuture($transaction->getAutoConfirmationDatetime())) :
                        __('Pending');

            case Transaction::STATUS_CONFIRMED:
                if ($transaction->output_balance_expires_at && $transaction->output_balance_expires_at->isPast()) {
                    return __('Burned');
                }

                return __('Approved');

            case Transaction::STATUS_REJECTED:
                return __('Declined');

            default:
                return __('Error');
        }
    }

    public static function getBalanceChangeSign(Transaction $transaction)
    {
        if ($transaction->action) {
            return '+';
        } else {
            return '';
        }
    }

    public static function getOrderStr(Transaction $transaction)
    {
        if ($transaction->action_id && $transaction->action->isOrderBased()) {
            return '('
                .mb_strtolower(__('Total Amount'))
                .HAmount::fShort($transaction->sourceStoreEntity->getDataProcessor()->getAmountTotal(), $transaction->partner->currency)
                .')';
        }

        return '';
    }

    public static function getStatuses(): array
    {
        return [
            Transaction::STATUS_PENDING => __('Pending'),
            Transaction::STATUS_REJECTED => __('Declined'),
            Transaction::STATUS_CONFIRMED => __('Approved'),
        ];
    }
}
