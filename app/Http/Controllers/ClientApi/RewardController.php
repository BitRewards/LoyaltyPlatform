<?php

namespace App\Http\Controllers\ClientApi;

use App\DTO\Factory\RewardFactory;
use App\DTO\Factory\TransactionHistoryFactory;
use App\DTO\Factory\WithdrawHistoryFactory;
use App\Exceptions\RewardAcquiringException;
use App\Fractal\Transformer\PartnerRewardTransformer;
use App\Http\Controllers\ClientApiController;
use App\Http\Requests\BitrewardsDepositRequest;
use App\Http\Requests\BitrewardsPayoutRequest;
use App\Models\Partner;
use App\Models\Reward;
use App\Services\RewardPayoutService;
use App\Services\RewardProcessors\BitrewardsPayout;
use App\Services\RewardService;
use App\Transformers\RewardTransformer;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class RewardController extends ClientApiController
{
    /**
     * @var RewardTransformer
     */
    private $rewardTransformer;

    /**
     * @var RewardService
     */
    private $rewardService;

    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var RewardFactory
     */
    private $rewardFactory;

    /**
     * @var PartnerRewardTransformer
     */
    private $partnerRewardTransformer;

    /**
     * @var TransactionHistoryFactory
     */
    private $transactionHistoryFactory;

    /**
     * @var RewardPayoutService
     */
    private $rewardPayoutService;

    /**
     * @var WithdrawHistoryFactory
     */
    private $withdrawHistoryFactory;

    public function __construct(
        RewardTransformer $rewardTransformer,
        RewardService $rewardService,
        Auth $auth,
        RewardFactory $rewardFactory,
        PartnerRewardTransformer $partnerRewardTransformer,
        TransactionHistoryFactory $transactionHistoryFactory,
        RewardPayoutService $rewardPayoutService,
        WithdrawHistoryFactory $withdrawHistoryFactory
    ) {
        $this->rewardTransformer = $rewardTransformer;
        $this->rewardService = $rewardService;
        $this->auth = $auth;
        $this->rewardFactory = $rewardFactory;
        $this->partnerRewardTransformer = $partnerRewardTransformer;
        $this->transactionHistoryFactory = $transactionHistoryFactory;
        $this->rewardPayoutService = $rewardPayoutService;
        $this->withdrawHistoryFactory = $withdrawHistoryFactory;
    }

    public function getList(Partner $partner): JsonResponse
    {
        $rewards = $this
            ->rewardService
            ->getPartnerRewardsForUser(
                $partner,
                $this->auth::user(),
                [Input::get('tag', ''), '*']
            );
        $rewards = $this->rewardFactory->factoryCollection($rewards);

        return $this->responseJsonCollection(
            fractal($rewards, $this->partnerRewardTransformer)
        );
    }

    public function get(Reward $reward): JsonResponse
    {
        if (Reward::STATUS_ENABLED != $reward->status) {
            abort(404);
        }
        $reward->load('specialOfferReward');

        $rewardData = $this->rewardFactory->factory($reward);

        return $this->responseJson(
            fractal($rewardData, $this->partnerRewardTransformer)
        );
    }

    /**
     * @param BitrewardsPayout $request
     *
     * @return JsonResponse
     *
     * @throws RewardAcquiringException
     */
    public function bitrewardsPayout(BitrewardsPayoutRequest $request)
    {
        try {
            $transaction = $this->rewardPayoutService->bitrewardsPayout(
                $request->partner,
                strtolower(trim($request->get('withdraw_eth'))),
                $request->token_amount
            );
        } catch (RewardAcquiringException $e) {
            return $this->badRequest($e->getMessage());
        }

        return $this->responseJson($this->withdrawHistoryFactory->factory($transaction));
    }

    public function confirmDeposit(BitrewardsDepositRequest $request, RewardPayoutService $rewardPayoutService)
    {
        try {
            $result = $rewardPayoutService->confirmDeposit($request->partner, $request->deposit_magic);

            return \jsonResponse($result);
        } catch (\Exception $ex) {
            return $this->responseError(Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage(), null);
        }
    }

    public function acquire(Reward $reward)
    {
        /**
         * @var User
         */
        $user = $this->auth::user();

        $transaction = $reward->getRewardProcessor()->acquire($user);

        return $this->responseJson(
            $this->transactionHistoryFactory->factory($transaction)
        );
    }
}
