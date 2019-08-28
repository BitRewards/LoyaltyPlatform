<?php

namespace App\Services;

use App\Administrator;
use App\DTO\CustomBonusData;
use App\DTO\CredentialData;
use App\Enums\ConfirmationStatus;
use App\Events\Bonuses\CustomBonusGiven;
use App\Exceptions\ValidationConstraintException;
use App\Jobs\NotifyUserAboutBalanceChange;
use App\Jobs\PushToExternalEmailService;
use App\Mail\OborotEmail;
use App\Models\Action;
use App\Models\Code;
use App\Models\Credential;
use App\Models\Notification;
use App\Models\StoreEvent;
use App\Models\Partner;
use App\DTO\StoreEventData;
use App\DTO\SocialNetworkProfile;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Giftd\ApiClient;
use App\Services\Persons\PersonFinder;
use App\Services\Persons\PersonGenerator;
use Carbon\Carbon;
use App\Models\Token;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{
    const STATIC_PASSWORD_SALT = 'gah6UThu';

    const MIN_PASSWORD_LENGTH = 7;

    const SOCIAL_NETWORK_PROFILE_WITHOUT_EMAIL_OR_PHONE_SESSION_KEY = 'social-network-profile';

    const USER_REFERRER_KEY_CLIENT_PARAM = 'gcrm_ref_user_key';
    const USER_REFERRER_KEY_SESSION_KEY = 'referrer_user_key';

    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var Hash
     */
    private $hash;

    /**
     * @var Factory
     */
    private $validationFactory;

    /**
     * @var Token
     */
    private $tokenModel;

    /**
     * @var User
     */
    private $userModel;

    /**
     * @var PartnerService
     */
    private $partnerService;

    /**
     * @var PersonGenerator
     */
    private $personGenerator;

    public function __construct(
        Auth $auth,
        Hash $hash,
        Factory $validationFactory,
        Token $tokenModel,
        User $userModel,
        PartnerService $partnerService,
        PersonGenerator $personGenerator
    ) {
        $this->auth = $auth;
        $this->hash = $hash;
        $this->validationFactory = $validationFactory;
        $this->tokenModel = $tokenModel;
        $this->userModel = $userModel;
        $this->partnerService = $partnerService;
        $this->personGenerator = $personGenerator;
    }

    public function createMainAdministratorForPartner(Partner $partner, string $password = null): array
    {
        $administrator = new Administrator();

        if (!$password) {
            $password = str_random(10);
        }

        $administrator->partner_id = $partner->id;
        $administrator->email = $partner->email;
        $administrator->role = Administrator::ROLE_PARTNER;
        $administrator->name = $partner->title;
        $administrator->password = $this->getPasswordHash($password);
        $administrator->is_main = true;
        $administrator->save();

        return compact('administrator', 'password');
    }

    public function setPasswordForPartner(Partner $partner, string $password)
    {
        $partner->mainAdministrator->password = \Hash::make(($password).self::STATIC_PASSWORD_SALT);
        $partner->mainAdministrator->save();
    }

    public function updateReferralLink(User $user)
    {
        if (!$user->partner_id) {
            // \Log::debug("Partner is not set for user. Can't update referral link!", [$user]);
            return null;
        }
        $actionProcessor = $user->partner->getOrderReferralActionProcessor();

        if (!$actionProcessor || !$user->partner->isConnectedToGiftdApi()) {
            /*if ( $user->partner->giftd_api_key && $user->partner->giftd_user_id ) {
                \Log::debug( "No actionProcessor" );
            } else {
                \Log::debug( "Partner is not connected to Giftd API" );
            }*/
            return null;
        }

        $createPromoCode = $user->partner->isFiatReferralEnabled();

        try {
            $client = ApiClient::create($user->partner);

            $result = $client->queryCrm('referral/getLink', [
                'crm_action_id' => $actionProcessor->getAction()->id,
                'crm_user_key' => $user->key,
                'value' => $actionProcessor->getReferralRewardValue() ?: 0,
                'value_type' => $actionProcessor->getReferralRewardValueType() ?: Action::VALUE_TYPE_FIXED,
                'min_amount_total' => $actionProcessor->getReferralRewardValueMinAmountTotal(),
                'lifetime' => $actionProcessor->getReferralRewardValueLifetime(),
                'card_id' => $actionProcessor->getAction()->getGiftdCardId(),
                'create_referral_promo_code' => $createPromoCode ? 1 : 0,
            ]);

            $url = $result['url'] ?? null;
            $promoCode = $result['promoCode'] ?? null;

            if ($url || $promoCode) {
                $user->referral_link = $url ?: $user->referral_link;
                $user->referral_promo_code = $promoCode ?: $user->referral_promo_code;
                $user->save();
            }
        } catch (\Exception $e) {
            \Log::debug('Exception on receiving referral_link', [$e]);

            return null;
        }

        return $url;
    }

    public function calculateUserBalance(User $user): float
    {
        return (float) Transaction::model()
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('status', Transaction::STATUS_CONFIRMED);
            })
            ->orWhere(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('status', Transaction::STATUS_PENDING)
                    ->whereNotNull('reward_id');
            })
            ->sum('balance_change');
    }

    public function recalculateBalance(User $user, $preventNotification = false): User
    {
        $balanceBefore = $user->balance;

        \HContext::setPartner($user->partner);
        $user->balance = $this->calculateUserBalance($user);

        if ($user->balance != $balanceBefore) {
            $user->save();

            if (!$preventNotification && $user->email && $user->balance > 0) {
                dispatch(new NotifyUserAboutBalanceChange($user->id, $balanceBefore));
            }

            $this->flushBasicUserDataCache($user->key);

            if ($user->partner->isBitrewardsDemoPartner()) {
                $this->notifyBitrewards($user);
            }
        }

        \HContext::restorePartner();

        return $user;
    }

    public function notifyBitrewards(User $user)
    {
        $email = $user->email;
        $name = $user->name;
        $points = $user->balance;
        $data = compact('email', 'name', 'points');

        try {
            \HHttp::doPost(
                'https://stimulator.bitrewards.network/investors/bat8iomi5uJohtoc/demoAppUpdate',
                $data
            );
        } catch (\Exception $e) {
        }
    }

    /**
     * @param User $user
     * @param $action
     * @param array     $additionalData
     * @param User|null $actor
     *
     * @return StoreEvent
     */
    public function processSystemEvent(User $user, $action, $additionalData = [], Authenticatable $actor = null)
    {
        $result = new StoreEventData(
            $action,
            array_merge($additionalData, [
                'userCrmKey' => $user->key,
            ])
        );

        if (!is_null($actor)) {
            $result->actorId = $actor->getAuthIdentifier();
        }

        $service = app(StoreEventService::class);
        $event = $service->saveAndHandle($user->partner, $result);

        return $event;
    }

    /**
     * @param CustomBonusData $dto
     *
     * @return StoreEvent
     */
    public function giveCustomBonusToUser(CustomBonusData $dto)
    {
        $partner = $dto->receiver->partner;

        if ($partner->isBitrewardsEnabled() && 'production' === \App::environment()) {
            // disable custom bonuses for bitrewards on production
            logger(__('Partner "%s" has BIT tokens enabled ("bitrewards-enabled": true) in settings. Custom bonus in BIT tokens has been disabled for security reasons'));

            return null;
        }

        $data = ['amount' => $dto->bonus];

        if ($dto->action) {
            $data[StoreEventData::DATA_KEY_TARGET_ACTION_ID] = $dto->action->id;
        }

        if ($dto->code) {
            $data[StoreEventData::DATA_KEY_SOURCE_CODE_ID] = $dto->code->id;
        }

        if ($dto->comment) {
            $data[StoreEventData::DATA_KEY_TRANSACTION_COMMENT] = $dto->comment;
        }

        if ($dto->tag) {
            $data[StoreEventData::DATA_KEY_TRANSACTION_TAG] = $dto->tag;
        }

        $event = $this->processSystemEvent($dto->receiver, StoreEvent::ACTION_CUSTOM_BONUS, $data, $dto->actor);

        $dto->receiver->refresh();

        event(new CustomBonusGiven($dto->receiver->partner, $dto));

        return $event;
    }

    public function createClient(CredentialData $credentialData, Partner $partner)
    {
        \DB::beginTransaction();

        $user = new User();
        $user->partner_id = $partner->id;
        $user->balance = 0;
        $user->referrer_id = $this->extractReferrerId($credentialData, $partner);

        $finder = new PersonFinder();
        $person = $finder->findPersonByUserData($credentialData, $partner->partner_group_id);

        if (!$person) {
            $person = $this->personGenerator->createPersonForUser($user, $partner->partner_group_id);
            $person->save();
        }

        if ($credentialData->phone) {
            $credentialData->phone = \HUser::normalizePhone($credentialData->phone, $partner->default_country);
        }

        $credentials = Credential::makeFromUserData($credentialData, $person, $partner->partner_group_id);

        if (empty($credentials)) {
            abort(400, __('Either email, phone or a social network should be passed'));
        }

        $user->person_id = $person->id;

        $userData = (array) $credentialData;

        unset($userData['referrer_id'], $userData['referrer_key']);

        $user->fill($userData);

        if ($user->password) {
            $user->password = $this->getPasswordHash($user->password);
        }

        if ($user->email) {
            $user->email = \HUser::normalizeEmail($user->email);
        }

        if ($user->phone) {
            $strictPhoneParsing = $user->email ? true : false;
            // we may use strict mode (nullify phone) only if we have user's email
            $user->phone = \HUser::normalizePhone($user->phone, $user->partner->default_country, $strictPhoneParsing);
        }

        $user->signup_type = $credentialData->signup_type ?: User::SIGNUP_TYPE_ORGANIC;

        $userData = [];

        if ($credentialData->utm_campaign) {
            $userData[User::DATA_UTM_CAMPAIGN] = $credentialData->utm_campaign;
        }

        if ($credentialData->utm_source) {
            $userData[User::DATA_UTM_SOURCE] = $credentialData->utm_source;
        }

        if ($credentialData->utm_medium) {
            $userData[User::DATA_UTM_MEDIUM] = $credentialData->utm_medium;
        }

        if ($credentialData->utm_term) {
            $userData[User::DATA_UTM_TERM] = $credentialData->utm_term;
        }

        if ($credentialData->utm_content) {
            $userData[User::DATA_UTM_CONTENT] = $credentialData->utm_content;
        }

        $user->data = $userData;
        $user->save();

        $this->processSystemEvent($user, StoreEvent::ACTION_SIGNUP);

        $user->refresh();

        if ($user->email && $partner->isOborotPromoPartner()) {
            \Mail::queue(new OborotEmail($user));
        }

        \DB::commit();

        $this->updateReferralLink($user);

        if ($user->email && $partner->getSetting(Partner::SETTINGS_IS_PUSHING_TO_EXTERNAL_EMAIL_SERVICES_ENABLED)) {
            dispatch(new PushToExternalEmailService($user->id));
        }

        return $user;
    }

    /**
     * @param CredentialData $data
     *
     * @return int
     */
    protected function extractReferrerId(CredentialData $data, Partner $partner): ?int
    {
        $referrerId = null;
        $referrer = null;

        // Let's check optional `referrer_id` and `referrer_key` fields of
        // given UserData object. If any of it contains value, save ID
        // or retrieve User by given key and select ID from result.

        if (!is_null($data->referrer_id)) {
            $referrerId = $data->referrer_id;
        } elseif (!is_null($data->referrer_key)) {
            $referrer = User::where('key', $data->referrer_key)->first();
            $referrerId = $referrer->id ?? 0;
        }

        // Next, we'll check if any ID was found, and if not,
        // we'll simply return 0 - this will mean that user
        // was signed up without any referrer data.

        if (0 === $referrerId) {
            return null;
        }

        // Otherwise check if we're already have loaded
        // Referrer object and get ID from it, or try
        // to load Referrer from DB with saved ID.

        $referrer = $referrer ?? User::find($referrerId);

        if (is_null($referrer)) {
            return null;
        }

        // Finally, validate that Referrer User and
        // new User are belong to the same Partner.
        // If yes, we've found correct Referrer.

        return $referrer->partner_id === $partner->id ? ($referrerId ?: null) : null;
    }

    private function getBasicUserDataCacheKey($userKey)
    {
        return 'user-data-9/'.$userKey;
    }

    public function getBasicUserData(Partner $partner, $userKey, $flushCache = false)
    {
        $cacheKey = $this->getBasicUserDataCacheKey($userKey);

        if (!$flushCache) {
            $data = \LaraRedis::get($cacheKey);

            if (null !== $data) {
                return unserialize($data);
            }
        }

        $user = User::where('key', $userKey)->first();

        if (!$user) {
            return null;
        }

        $balanceBig = \HAmount::shouldBeShortened($user->balance);

        if ($partner->isFiatReferralEnabled()) {
            $balance = $user->getFiatBalanceAvailableToWithdraw();
            $balanceStr = floor($balance);
            $currency = \HAmount::sShort($partner->currency);
        } else {
            $balance = floatval($user->balance ?? 0);
            $balanceStr = \HAmount::shorten($balance);
            $currency = $partner->isBitrewardsEnabled() ? 'BIT' : __('{point|points}', ['count' => $balanceBig ? 1000000 : round($user->balance)]);
        }

        \HLanguage::setLanguage($partner->default_language);
        $data = [
            'balance' => $balance,
            'name' => $user->name ? \HUser::getFirstName($user->name) : null,
            'picture' => \HUser::getPictureOrPlaceholder($user),
            'balanceStr' => $balanceStr,
            'currency' => $currency,
            'referralLink' => $user->referral_link,
            'referralPromoCode' => $user->referral_promo_code,
        ];
        \HLanguage::restorePreviousLanguage();

        \LaraRedis::set($cacheKey, serialize($data), 'EX', 3600 * 24 * 2);

        return $data;
    }

    public function flushBasicUserDataCache($userKey)
    {
        \LaraRedis::del($this->getBasicUserDataCacheKey($userKey));
    }

    public function storeSocialNetworkProfileWithoutEmailOrPhone(SocialNetworkProfile $socialNetworkProfile)
    {
        session()->put(self::SOCIAL_NETWORK_PROFILE_WITHOUT_EMAIL_OR_PHONE_SESSION_KEY, $socialNetworkProfile);
    }

    public function getSocialNetworkProfileWithoutEmailOrPhone()
    {
        return session()->get(self::SOCIAL_NETWORK_PROFILE_WITHOUT_EMAIL_OR_PHONE_SESSION_KEY);
    }

    public function forgetSocialNetworkProfileWithoutEmailOrPhone()
    {
        session()->forget(self::SOCIAL_NETWORK_PROFILE_WITHOUT_EMAIL_OR_PHONE_SESSION_KEY);
    }

    /**
     * Merges $second into $first.
     *
     * @param User $first
     * @param User $second
     * @param bool $allowMergingNonEmptyUsers
     */
    public function merge(User $first, User $second, $allowMergingNonEmptyUsers = false)
    {
        if ($first->role || $second->role) {
            \Log::alert('Unable to merge users with role', compact('first', 'second'));

            return;
        }

        // that was an argument, leaving it as a constant variable just in case
        $allowContactsMerge = true;

        if ($second->isEmptyUser() || $allowMergingNonEmptyUsers) {
            \DB::beginTransaction();

            if (!is_null($second->id) && intval($first->partner_id) > 0) {
                Code::where('user_id', $second->id)
                    ->where('partner_id', $first->partner_id)
                    ->update([
                        'user_id' => $first->id,
                        'acquired_at' => Carbon::now(),
                    ]);
            }

            if ($allowContactsMerge) {
                if (!$first->phone && $second->phone) {
                    $first->phone = $second->phone;
                    $first->phone_confirmed_at = $second->phone_confirmed_at;
                }

                if (!$first->email && $second->email) {
                    $first->email = $second->email;
                    $first->email_confirmed_at = $second->email_confirmed_at;
                }
            }

            if (!$first->picture && $second->picture) {
                $first->picture = $second->picture;
            }

            $transactions = $second->transactions;

            foreach ($transactions as $transaction) {
                if ($transaction->action) {
                    if (!$transaction->source_store_event_id) {
                        throw new \RuntimeException("Transaction {$transaction->id} does not have source_store_event_id, unable to merge users {$first->id} and {$second->id}");
                    }
                    $sourceEvent = $transaction->sourceStoreEvent;

                    if (!app(ActionService::class)->checkLimits($transaction->action, $first, $sourceEvent)) {
                        $transaction->status = Transaction::STATUS_REJECTED;
                    }
                }
                $transaction->user_id = $first->id;
                $transaction->save();
            }

            if ($second->exists()) {
                $this->recalculateBalance($second);
                $second->delete();
            }
            $this->recalculateBalance($first);

            \DB::commit();
        } else {
            \Log::alert('Unable to merge non-empty users!', compact('first', 'second'));
        }
    }

    public function getOrderCommentTracking(User $user)
    {
        return ($user->codes && count($user->codes)) ? $user->codes[0]->token : $user->partner_id.'-'.$user->id;
    }

    public function getConfirmationStatus(User $user)
    {
        switch ($authMethod = $user->partner->getAuthMethod()) {
            case Partner::AUTH_METHOD_EMAIL:
                if ($user->isEmailConfirmed() || !$user->email) {
                    return ConfirmationStatus::NOT_NEEDED;
                } else {
                    return ConfirmationStatus::CONFIRM_EMAIL;
                }

                break;

            case Partner::AUTH_METHOD_PHONE:
                if ($user->isPhoneConfirmed() || !$user->phone) {
                    return ConfirmationStatus::NOT_NEEDED;
                } else {
                    return ConfirmationStatus::CONFIRM_PHONE;
                }

                break;

            default:
                throw new \RuntimeException("Unknown auth method: $authMethod");
        }
    }

    public function isUserWithoutConfirmedEmailOrPhone(User $user = null)
    {
        if (!$user || !$user->partner) {
            return false;
        }

        if ($user->partner->isAuthMethodEmail()) {
            return empty($user->email) || !$user->person->isEmailConfirmed($user->email);
        }

        if ($user->partner->isAuthMethodPhone()) {
            return empty($user->phone) || !$user->person->isPhoneConfirmed($user->phone);
        }

        return false;
    }

    public function isUserConfirmed(User $user = null)
    {
        if ($user && $user->partner) {
            if ($user->partner->isAuthMethodPhone()) {
                return $user->isPhoneConfirmed();
            } else {
                return $user->isEmailConfirmed();
            }
        }

        return false;
    }

    public function getAutologinToken(User $user, $destinationType = null, $destination = null)
    {
        $query = Token
            ::where('owner_user_id', $user->id)
            ->where('type', Token::TYPE_AUTO_LOGIN);

        if ($destinationType) {
            $query->where('destination', $destination);
            $query->where('destination_type', $destinationType);
        }

        $existingToken = $query->first();

        if ($existingToken && !$existingToken->isExpired()) {
            return $existingToken->token;
        } else {
            return Token::add($user->id, Token::TYPE_AUTO_LOGIN, $destination, $destinationType);
        }
    }

    /**
     * @deprecated Possible deprecated
     *
     * @param Partner $partner
     *
     * @return User
     */
    public function getTestClientNoRole(Partner $partner = null)
    {
        if (!$partner) {
            $partner = app(PartnerService::class)->getTestPartner();
        }

        $user = new User();
        $user->partner = $partner;
        $user->balance = 100;
        $user->name = 'Alex';
        $user->email = 'john@example.com';
        $user->phone = '+1 777 000 9999';
        $user->referral_link = 'https://giftd.tech/r/L-BDDYozWPOd';

        $signupAction = $partner->getSignupAction();
        $cashbackAction = $partner->findActionByType(Action::TYPE_ORDER_CASHBACK);

        $transaction1 = (new Transaction())->fill([
            'balance_change' => $signupAction->value,
            'type' => Transaction::TYPE_ACTION,
            'action_id' => $signupAction->id,
            'status' => Transaction::STATUS_CONFIRMED,
        ]);
        $transaction1->created_at = Carbon::now()->subHour();

        $transactionList = [$transaction1];

        if ($cashbackAction) {
            $transaction2 = (new Transaction())->fill([
                'balance_change' => 355,
                'action_id' => $cashbackAction->id,
                'type' => Transaction::TYPE_ACTION,
                'status' => Transaction::STATUS_PENDING,
            ]);
            $transaction2->created_at = Carbon::now()->subMinutes(59);

            $transaction2 = \Mockery::instanceMock($transaction2);
            $transaction2
                ->shouldReceive('getAutoConfirmationDatetime')
                ->andReturn(Carbon::now()->addWeeks(2));

            array_unshift($transactionList, $transaction2);
        }

        $user = \Mockery::instanceMock($user);
        $user->shouldReceive('getLastTransactions')
             ->andReturn(collect($transactionList));

        return $user;
    }

    public function findByKey($key)
    {
        return User::model()->findByKey($key);
    }

    public function findByBitAddress($address)
    {
        if (empty($address)) {
            return false;
        }

        return User::where(['bit_tokens_sender_address' => $address])->first();
    }

    public function findByEthAddress($address)
    {
        if (empty($address)) {
            return false;
        }

        return User::where(['eth_sender_address' => $address])->first();
    }

    public function confirmResetPassword(Partner $partner, $emailOrPhone, $token, $password)
    {
        $validator = $this->validationFactory->make(
            [
                'emailOrPhone' => $emailOrPhone,
                'token' => $token,
                'password' => $password,
            ],
            [
                'token' => 'required',
                'emailOrPhone' => 'required',
                'password' => 'required|min:7',
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $token = $this->tokenModel::check(
            $token,
            Token::TYPE_RESET_PASSWORD,
            $partner->isAuthMethodPhone() ? Token::DESTINATION_TYPE_PHONE : Token::DESTINATION_TYPE_EMAIL,
            $emailOrPhone
        );

        /** @var User $user */
        $user = $token->owner ?? null;

        if (!$user) {
            throw new ValidationConstraintException('token', __('Token or token owner not found'));
        }

        if ($user) {
            if ($partner->isAuthMethodPhone()) {
                $credential = $user->person->credentials->where('phone', '=', $emailOrPhone)->first();
            } else {
                $credential = $user->person->credentials->where('email', '=', $emailOrPhone)->first();
            }

            if (!$credential) {
                throw new ValidationConstraintException('emailOrPhone', __('No such credential found'));
            }

            $credential->confirmed_at = Carbon::now();
            $credential->setPassword($password);
            $credential->save();

            //@todo truncate user auth tokens
            $this->auth::login($user);
        }
    }

    public function updateBitTokenAddress(User $user, string $ethAddress): void
    {
        $ethAddress = \HStr::normalizeEthNumber($ethAddress);

        if ($ethAddress === $user->bit_tokens_sender_address) {
            return;
        }

        if ($this->findByBitTokenAddress($ethAddress)
            || $this->partnerService->findByEthereumAddress($ethAddress)
        ) {
            throw new \InvalidArgumentException(__('Wallet is already in use by another user'));
        }

        $user->bit_tokens_sender_address = $ethAddress;
        $user->saveOrFail();
    }

    public function updateEthereumAddress(User $user, string $ethAddress): void
    {
        $ethAddress = \HStr::normalizeEthNumber($ethAddress);

        if ($ethAddress === $user->eth_sender_address) {
            return;
        }

        if ($this->findByEthAddress($ethAddress)
            || $this->partnerService->findByEthereumAddress($ethAddress)
        ) {
            throw new \InvalidArgumentException(__('Wallet is already in use by another user'));
        }

        $user->eth_sender_address = $ethAddress;
        $user->saveOrFail();
    }

    public function findByBitTokenAddress(string $bitTokenAddress): ?User
    {
        $bitTokenAddress = \HStr::normalizeEthNumber($bitTokenAddress);

        return User::where('bit_tokens_sender_address', '=', $bitTokenAddress)->first();
    }

    public function findByEthereumAddress(string $ethereumAddress): ?User
    {
        $ethereumAddress = \HStr::normalizeEthNumber($ethereumAddress);

        return User::where('eth_sender_address', '=', $ethereumAddress)->first();
    }

    public function confirmEmailFor(User $user): bool
    {
        if ($user->isEmailConfirmed()) {
            return true;
        }

        return \DB::transaction(function () use ($user) {
            $user->email_confirmed_at = new Carbon();
            $user->person->confirmEmail($user->email);

            return $user->save();
        });
    }

    public function confirmPhoneFor(User $user): bool
    {
        if ($user->isPhoneConfirmed()) {
            return true;
        }

        return \DB::transaction(function () use ($user) {
            $user->phone_confirmed_at = new Carbon();
            $user->person->confirmPhone($user->phone);

            return $user->save();
        });
    }

    public function findPartnerUser(Partner $partner, string $credential): ?User
    {
        switch ($partner->getAuthMethod()) {
            case Partner::AUTH_METHOD_EMAIL:
                $user = $this->userModel->findByPartnerAndEmail($partner, $credential);

                break;

            case Partner::AUTH_METHOD_PHONE:
                $user = $this->userModel->findByPartnerAndPhone($partner, $credential);

                break;

            default:
                throw new \DomainException("Auth method '{$partner->getAuthMethod()}' not supported");
        }

        return $user;
    }

    public function getUsersForBurningPointsSummaryNotification(
        int $defaultWeekday = 2,
        string $defaultTimeFormUTC = '12:00:00',
        string $defaultTimeForMSK = '10:15:00'
    ): Collection {
        $notifyType = Notification::TYPE_BURNING_POINTS_SUMMARY;

        // @todo move parameters to bindings
        $sql = <<<SQL
WITH partner_notification_settings as (
  SELECT
    id,
    COALESCE((partner_settings->>'burn-point-notify-weekday')::integer, {$defaultWeekday}) as weekday,
    COALESCE(
      (partner_settings->>'burn-point-notify-time')::TIME,
      CASE default_country
        WHEN 'ru' THEN
          TIME WITH TIME ZONE '{$defaultTimeForMSK}' AT TIME ZONE 'MSK'
        ELSE
          TIME WITH TIME ZONE '{$defaultTimeFormUTC}' AT TIME ZONE 'UTC'
        END
    ) as notify_time
  FROM partners
)
SELECT
    u.*
FROM transactions t
INNER JOIN partner_notification_settings s ON t.partner_id = s.id
INNER JOIN users u ON t.user_id = u.id
LEFT JOIN notifications n 
  ON 
    t.user_id = n.user_id 
  AND 
    n.type = '{$notifyType}' 
  AND 
    n.created_at BETWEEN (NOW() - '1 day'::INTERVAL) AND NOW()
WHERE
    t.output_balance > 0
  AND
    t.output_balance_expires_at < NOW() + '14 days'::INTERVAL
  AND 
    EXTRACT(DOW FROM NOW()) = s.weekday
  AND
    s.notify_time BETWEEN CURRENT_TIME - '1 hour'::INTERVAL AND CURRENT_TIME
  AND n.id IS NULL 
GROUP BY u.id
SQL;

        return User::hydrate(\DB::select($sql));
    }

    public function getPasswordHash(string $password): string
    {
        return \Hash::make($password.self::STATIC_PASSWORD_SALT);
    }
}
