<?php

namespace App\Http\Controllers\Api;

use App\DTO\CustomBonusData;
use App\DTO\CredentialData;
use App\Http\Requests\AbstractBonusRequest;
use App\Http\Requests\Api\GiveBonusExtendedRequest;
use App\Models\User;
use App\Services\UserService;
use App\Http\Controllers\Controller;
use App\Transformers\UserTransformer;
use App\Http\Requests\Api\GiveBonusRequest;

class UserBonusController extends Controller
{
    /**
     * @var UserService
     */
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Give bonus to user.
     *
     * @param \App\Http\Requests\Api\GiveBonusRequest $request
     * @param \App\Services\UserService               $userService
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(GiveBonusRequest $request)
    {
        return $this->handleBonusRequest($request);
    }

    public function storeExtended(GiveBonusExtendedRequest $request)
    {
        return $this->handleBonusRequest($request);
    }

    protected function createUserByBonusRequest(GiveBonusExtendedRequest $request)
    {
        $data = CredentialData::make([
            'email' => $request->getEmail(),
            'phone' => $request->getPhoneNumber(),
            'name' => $request->getUserName(),
            'password' => null,
            'signup_type' => User::SIGNUP_TYPE_GIVE_BONUS,
        ]);

        return $this->userService->createClient($data, $request->user()->partner);
    }

    protected function handleBonusRequest(AbstractBonusRequest $request)
    {
        $user = $request->bonusReceiver();

        if (!$user && $request instanceof GiveBonusExtendedRequest) {
            $user = $this->createUserByBonusRequest($request);
        }

        if (!$user) {
            return response()->json([
                'error' => __('User was not found'),
            ], 404);
        }

        $event = $this->userService->giveCustomBonusToUser(
            new CustomBonusData(
                $user,
                $request->getBonusAmount(),
                \Auth::user(),
                $request->bonusAction(),
            null,
                $request->getComment()
            )
        );
        $eventId = null === $event ? $event->id : 0;

        return response()->json(
            fractal($user, (new UserTransformer())->setRequestData(['event_id' => $eventId]))
        );
    }
}
