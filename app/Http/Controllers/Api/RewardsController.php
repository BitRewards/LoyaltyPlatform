<?php

namespace App\Http\Controllers\Api;

use App\Administrator;
use App\Http\Requests\Api\Rewards\StoreRewardRequest;
use App\Http\Requests\Api\Rewards\UpdateRewardRequest;
use App\Models\User;
use App\Models\Reward;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Transformers\RewardTransformer;
use App\Http\Requests\Api\AcquireReward;
use App\Transformers\TransactionTransformer;
use App\Http\Requests\Api\GetAvailableRewards;
use App\Http\Controllers\Api\Traits\ProcessesTransactions;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RewardsController extends ApiController
{
    use ProcessesTransactions;

    /**
     * Show available rewards.
     *
     * @param \App\Http\Requests\Api\GetAvailableRewards $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(GetAvailableRewards $request)
    {
        $partner = $request->user()->partner;
        $user = $request->has('user_key') ? $this->retrieveUser($request->input('user_key')) : null;
        $total = intval($request->input('total'));

        $query = Reward::model()
            ->whereAttributes([
                'partner_id' => $partner->id,
                'status' => Reward::STATUS_ENABLED,
            ])
            ->where(function (Builder $query) use ($user) {
                if (is_null($user)) {
                    return;
                }

                $query->where('price', '<=', $user->balance);
            });

        $rewards = $this->applyGlobalFilters($request, $query)->get()
            ->sort(function (Reward $a, Reward $b) use ($total) {
                if ($total) {
                    $diff = ($b->getRewardProcessor()->getDiscountPercent($total) - $a->getRewardProcessor()->getDiscountPercent($total));

                    if (0 != $diff) {
                        return $diff;
                    }
                }

                return $b->price - $a->price;
            });

        return response()->json(
            fractal($rewards, (new RewardTransformer())->setTotal($total))
        );
    }

    /**
     * Create new reward.
     *
     * @param StoreRewardRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRewardRequest $request)
    {
        $reward = Reward::create(
            array_merge(
                $this->inputsWithoutNulls($request, [
                    'title', 'price', 'type', 'value',
                    'value_type', 'status', 'tag', 'description',
                    'description_short', 'config',
                ]),
                ['partner_id' => $request->user()->partner->id]
            )
        );

        return response()->json(
            fractal($reward, new RewardTransformer())
        );
    }

    /**
     * Get reward by ID.
     *
     * @param \App\Models\User $user
     * @param int              $id
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return \App\Models\Reward
     */
    private function retrieveReward(Administrator $user, int $id)
    {
        $reward = Reward::whereId($id)->first();

        if (is_null($reward) || $reward->partner_id != $user->partner->id) {
            throw new NotFoundHttpException();
        }

        return $reward;
    }

    /**
     * Retrieve User by key.
     *
     * @param string $userKey
     *
     * @return \App\Models\User
     */
    private function retrieveUser(string $userKey)
    {
        $user = User::where('key', $userKey)->first();

        if (is_null($user)) {
            throw new NotFoundHttpException('User was not found.');
        }

        return $user;
    }

    /**
     * Get reward.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $rewardId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $rewardId)
    {
        $reward = $this->retrieveReward($request->user(), intval($rewardId));
        $total = intval($request->input('total'));

        return response()->json(
            fractal($reward, (new RewardTransformer())->setTotal($total))
        );
    }

    /**
     * Update the reward.
     *
     * @param UpdateRewardRequest $request
     * @param $rewardId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRewardRequest $request, $rewardId)
    {
        $reward = $this->retrieveReward($request->user(), $rewardId);

        $reward->update(
            $this->inputsWithoutNulls($request, [
                'title', 'price', 'type', 'value',
                'value_type', 'status', 'tag', 'description',
                'description_short', 'config',
            ])
        );

        return response()->json(
            fractal($reward->fresh(), new RewardTransformer())
        );
    }

    /**
     * @param \App\Http\Requests\Api\AcquireReward $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function acquire(AcquireReward $request, $rewardId)
    {
        $reward = $request->getReward();
        $user = $request->getUserByKey();
        $extraData = [
            Transaction::DATA_NOT_USABLE_BY_CLIENT => true,
            Transaction::DATA_LIFETIME_OVERRIDDEN => 10 * 60,
        ];

        $transaction = $reward->getRewardProcessor()->acquire($user, $extraData, $request->user());

        return response()->json(
            fractal($transaction, new TransactionTransformer())
        );
    }

    /**
     * Delete the reward.
     *
     * @param Request $request
     * @param $rewardId
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response*
     */
    public function destroy(Request $request, $rewardId)
    {
        $reward = $this->retrieveReward($request->user(), $rewardId);

        $reward->delete();

        return response('', 204);
    }
}
