<?php

namespace Bitrewards\ReferralTool\Filters;

use App\Models\Action;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\BooleanFilter;

class ReferralGroupFilter extends BooleanFilter
{
    public function apply(Request $request, $query, $value)
    {
        if ($value['withReferralPurchases']) {
            /** @var User $user */
            $user = $request->user();
            $subQuery = Transaction::query()
                ->select('user_id', \DB::raw('COUNT(*) as referral_purchases'))
                ->where('transactions.partner_id', $user->partner->id)
                ->where('actions.partner_id', $user->partner->id)
                ->join('actions', 'transactions.action_id', 'actions.id')
                ->where('actions.type', Action::TYPE_ORDER_REFERRAL)
                ->groupBy('transactions.user_id');

            $query->joinSub($subQuery, 'purchases', function (JoinClause $join) {
                $join->on('users.id', 'purchases.user_id');
            });
        }
    }

    public function options(Request $request)
    {
        return [
            __('With referral purchases') => 'withReferralPurchases',
        ];
    }

    public function name()
    {
        return __('Filter by actions');
    }
}
