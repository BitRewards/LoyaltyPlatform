<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\DTO\CredentialData;
use App\DTO\UserData;
use App\Exceptions\ValidationConstraintException;
use App\Models\Partner;
use App\Services\Giftd\ApiClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\CodeService;
use App\Services\UserService;
use App\Http\Controllers\Controller;
use App\Transformers\UserTransformer;
use App\Http\Requests\Api\CreateClientRequest;
use Illuminate\Http\Response;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Lock\Factory;

class UsersController extends Controller
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var Factory
     */
    protected $lockFactory;

    public function __construct(UserService $userService, Factory $lockFactory)
    {
        $this->userService = $userService;
        $this->lockFactory = $lockFactory;
    }

    /**
     * Create/merge user.
     *
     * @param CreateClientRequest $request
     * @param UserService         $userService
     * @param CodeService         $codeService
     *
     * @return JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(CreateClientRequest $request, UserService $userService, CodeService $codeService): JsonResponse
    {
        /** @var LoggerInterface $logger */
        $logger = app('user-store.api.log');
        $logger->debug($request->getRequestUri(), [
            'resourceKey' => $request->resourceKey(),
            'params' => $request->all(),
        ]);

        $resourceLock = $this->lockFactory->createLock($request->resourceKey(), 60);

        if (!$resourceLock->acquire(true)) {
            \Log::warning('Acquire lock failed', $request->all());

            return response(null, Response::HTTP_REQUEST_TIMEOUT);
        }

        $partner = $request->user()->partner;

        if (!$request->return_existing_user && $request->email && User::model()->findByPartnerAndEmail($partner, $request->email)) {
            return jsonError(['email' => __('Email already used by another user')]);
        }

        if (!$request->return_existing_user && $request->phone && User::model()->findByPartnerAndPhone($partner, $request->phone)) {
            return jsonError(['phone' => __('Phone number already used by another user')]);
        }

        \DB::beginTransaction();

        if ($request->has('token')) {
            $code = $codeService->findOrCreateCode($partner, $request->input('token'));

            if ($code->user) {
                return response()->json(
                    fractal($code->user, new UserTransformer())
                );
            }

            $user = $userService->createClient(CredentialData::make($request->all()), $partner);
            $codeService->acquire($user, $code);
        } else {
            // if doExtraValidation does not fails (when return_existing_user=1)
            // returns User info
            // see app/Http/Requests/Api/CreateClientRequest.php:43

            if ($request->return_existing_user) {
                if ($existingUserByEmail = User::model()->findByPartnerAndEmail($partner, $request->input('email'))) {
                    $autoLoginToken = app(UserService::class)->getAutologinToken($existingUserByEmail);

                    if ($request->force_email_confirmation) {
                        app(UserService::class)->confirmEmailFor($existingUserByEmail);
                    }

                    \DB::commit();

                    return response()->json(
                        fractal($existingUserByEmail, (new UserTransformer())->setAutoLoginToken($autoLoginToken))
                    );
                }

                if ($existingUserByPhone = User::model()->findByPartnerAndPhone($partner, $request->input('phone'))) {
                    $autoLoginToken = app(UserService::class)->getAutologinToken($existingUserByPhone);

                    return response()->json(
                        fractal($existingUserByPhone, (new UserTransformer())->setAutoLoginToken($autoLoginToken))
                    );
                }
            }

            $user = $userService->createClient(CredentialData::make($request->all()), $partner);
        }

        $userData = $this->createUserDataByRequest($request, $partner, $user);
        $this->mergeUserData($user, $userData);

        if (!$user->id) {
            $user->signup_type = User::SIGNUP_TYPE_API;
        }

        $user->save();

        \DB::commit();

        $autoLoginToken = null;

        if ($request->return_existing_user) {
            $autoLoginToken = app(UserService::class)->getAutologinToken($user);
        }

        if ($request->force_email_confirmation) {
            app(UserService::class)->confirmEmailFor($user);
        }

        return response()->json(
            fractal($user, (new UserTransformer())->setAutoLoginToken($autoLoginToken))
        );
    }

    protected function createUserDataByRequest(Request $request, Partner $partner, ?User $user = null): UserData
    {
        $userData = $user ? $this->createUserDataByUser($user) : UserData::make([
            'signup_type' => User::SIGNUP_TYPE_API,
        ]);

        $phoneNumber = \HUser::normalizePhone($request->input('phone'), $partner->default_country);
        $email = \HUser::normalizeEmail($request->input('email', ''));

        if (!$userData->name) {
            $userData->name = $request->input('name');
        }

        if (!$userData->email && $email) {
            $existingUser = User::model()->findByPartnerAndEmail($partner, $email);

            if ($user && $existingUser && $existingUser->id != $user->id) {
                throw new ValidationConstraintException('email', __('Email already used by another user'));
            }

            $userData->email = $email;
        }

        if (!$userData->phone && $phoneNumber) {
            $existingUser = User::model()->findByPartnerAndPhone($partner, $phoneNumber);

            if ($user && $existingUser && $existingUser->id != $user->id) {
                throw new ValidationConstraintException('phone', __('Phone number already used by another user'));
            }

            $userData->phone = $phoneNumber;
        }

        if ($promoCode = trim($request->input('promo_code'))) {
            try {
                $apiClient = ApiClient::create($partner);
                $result = $apiClient->check($promoCode);

                if ($result && $result->crm_ref_user_key) {
                    $referrer = User::model()->findByKey($result->crm_ref_user_key);

                    if ($referrer && $referrer->partner_id == $partner->id) {
                        $userData->referrer_id = $referrer->id;
                    }
                }
            } catch (\Throwable $e) {
                \Log::alert($e);
            }
        }

        return $userData;
    }

    protected function createUserDataByUser(User $user): UserData
    {
        return UserData::make([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
        ]);
    }

    protected function mergeUserData(User $user, UserData $userData): void
    {
        $user->name = $user->name ?: $userData->name;
        $user->email = $user->email ?: $userData->email;
        $user->phone = $user->phone ?: $userData->phone;
        $user->referrer_id = $user->referrer_id ?: $userData->referrer_id;
        $user->signup_type = $user->signup_type ?: $userData->signup_type;
    }

    /**
     * Get user by key.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $userKey
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $userKey)
    {
        $user = User::where('key', $userKey)->first();

        if (is_null($user)) {
            return response()->json([
                'error' => __('User was not found'),
            ], 404);
        }

        $authUser = $request->user();

        if ($user->partner_id != $authUser->partner_id && !$authUser->can('admin')) {
            throw new AccessDeniedHttpException();
        }

        return response()->json(
            fractal($user, (new UserTransformer()))
        );
    }
}
