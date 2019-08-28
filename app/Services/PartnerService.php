<?php

namespace App\Services;

use App\DTO\CustomBonusData;
use App\Http\Requests\Api\CreatePartnerRequest;
use App\Models\Action;
use App\Models\PartnerDeposit;
use App\Models\PartnerGroup;
use App\Models\Reward;
use App\Models\StoreEntity;
use App\Models\Partner;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Giftd\ApiClient;
use Bitrewards\ReferralTool\Traits\DefaultRangesTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PartnerService
{
    const DEFAULT_SIGNUP_BONUS = 100;

    const MASS_AWARD_NOTIFICATION_EMAILS = ['support@bitrewards.com'];
    const DEBUG_EMAIL = ['**REMOVED**'];

    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var ActionService
     */
    private $actionService;

    /**
     * @var
     */
    private $rewardService;

    /**
     * @var
     */
    private $transactionService;

    /**
     * @var HelpService
     */
    private $helpService;

    /**
     * @var \HCustomizations
     */
    private $customizationHelper;

    /**
     * @var \HUser
     */
    private $userHelper;

    /**
     * @var \HAmount
     */
    private $amountHelper;

    /**
     * @var \HStr
     */
    private $stringHelper;

    public function __construct(
        Auth $auth,
        ActionService $actionService,
        RewardService $rewardService,
        TransactionService $transactionService,
        HelpService $helpService,
        \HCustomizations $customizationHelper,
        \HUser $userHelper,
        \HAmount $amountHelper,
        \HStr $stringHelper
    ) {
        $this->auth = $auth;
        $this->actionService = $actionService;
        $this->rewardService = $rewardService;
        $this->transactionService = $transactionService;
        $this->helpService = $helpService;
        $this->customizationHelper = $customizationHelper;
        $this->userHelper = $userHelper;
        $this->amountHelper = $amountHelper;
        $this->stringHelper = $stringHelper;
    }

    public function signupPartner(CreatePartnerRequest $request)
    {
        \DB::beginTransaction();

        if (null !== $request->partner_group_id) {
            $partnerGroup = PartnerGroup::findOrFail($request->partner_group_id);
        } else {
            $partnerGroup = new PartnerGroup();
            $partnerGroup->name = $request->title;
            $partnerGroup->save();
        }

        $partner = new Partner();
        $partner->title = $request->title;
        $partner->email = $request->email;
        $partner->giftd_id = $request->giftd_id;
        $partner->giftd_api_key = $request->giftd_api_key;
        $partner->giftd_user_id = $request->giftd_user_id;
        $partner->customizations = $request->customizations;
        $partner->currency = $request->currency;
        $partner->url = $request->url;
        $partner->money_to_points_multiplier = (\HAmount::CURRENCY_USD == $partner->currency || \HAmount::CURRENCY_EUR == $partner->currency) ? 100 : 1;
        $partner->default_language = $request->default_language;
        $partner->default_country = \HLanguage::getDefaultCountryForLanguage($partner->default_language);
        $partner->currency = $request->currency;
        $partner->partner_settings = [
            Partner::SETTINGS_AUTO_CONFIRM_INTERVAL => [
                StoreEntity::TYPE_ORDER => 14 * 24 * 3600,
            ],
            // Partner::SETTINGS_AUTO_SIGNUP_USERS_FROM_ORDERS => true
        ];
        $partner->partner_group_id = $partnerGroup->id;
        $partner->save();

        ['administrator' => $administrator, 'password' => $password] = app(UserService::class)->createMainAdministratorForPartner($partner, $request->password);

        $this->createDefaultActions($partner);
        $this->createDefaultRewards($partner);

        app(HelpService::class)->createDefaultQuestions($partner);
        app(CustomizationsService::class)->migrateCustomizations($partner);

        \DB::commit();

        return compact('partner', 'password');
    }

    public function createShareInstagramAction(Partner $partner)
    {
        $action = new Action();
        $action->partner_id = $partner->id;
        $action->status = Action::STATUS_ENABLED;
        $action->type = Action::TYPE_SHARE_INSTAGRAM;
        $action->value = 100;
        $action->value_type = Action::VALUE_TYPE_FIXED;
        $action->is_system = true;
        $action->save();

        return $action;
    }

    public function createCustomSocialAction(Partner $partner)
    {
        $action = new Action();
        $action->partner_id = $partner->id;
        $action->status = Action::STATUS_ENABLED;
        $action->type = Action::TYPE_CUSTOM_SOCIAL_ACTION;
        $action->value = 100;
        $action->value_type = Action::VALUE_TYPE_FIXED;
        $action->is_system = true;
        $action->save();

        return $action;
    }

    public function createSubscribeTelegramAction(Partner $partner)
    {
        $action = new Action();
        $action->partner_id = $partner->id;
        $action->status = Action::STATUS_ENABLED;
        $action->type = Action::TYPE_SUBSCRIBE_TO_TELEGRAM;
        $action->value = 100;
        $action->value_type = Action::VALUE_TYPE_FIXED;
        $action->is_system = true;
        $action->save();

        return $action;
    }

    public function createSystemCustomBonusAction(Partner $partner)
    {
        $action = new Action();
        $action->partner_id = $partner->id;
        $action->status = Action::STATUS_ENABLED;
        $action->type = Action::TYPE_CUSTOM_BONUS;
        $action->value = 100;
        $action->value_type = Action::VALUE_TYPE_PERCENT;
        $action->is_system = true;
        $action->save();

        return $action;
    }

    public function createSystemRefillBitAction(Partner $partner)
    {
        $action = new Action();
        $action->partner_id = $partner->id;
        $action->status = Action::STATUS_ENABLED;
        $action->type = Action::TYPE_REFILL_BIT;
        $action->value = 100;
        $action->value_type = Action::VALUE_TYPE_PERCENT;
        $action->is_system = true;
        $action->save();

        return $action;
    }

    public function createSystemExchangeEthToBitAction(Partner $partner)
    {
        $action = new Action();
        $action->partner_id = $partner->id;
        $action->status = Action::STATUS_ENABLED;
        $action->type = Action::TYPE_EXCHANGE_ETH_TO_BIT;
        $action->value = 100;
        $action->value_type = Action::VALUE_TYPE_PERCENT;
        $action->is_system = true;
        $action->save();

        return $action;
    }

    public function createDefaultActions(Partner $partner, $signupBonus = self::DEFAULT_SIGNUP_BONUS)
    {
        $action = new Action();
        $action->partner_id = $partner->id;
        $action->status = Action::STATUS_ENABLED;
        $action->type = Action::TYPE_SIGNUP;
        $action->value = $signupBonus;
        $action->value_type = Action::VALUE_TYPE_FIXED;
        $action->is_system = false;
        $action->save();

        $this->createSystemCustomBonusAction($partner);
        $this->createSystemRefillBitAction($partner);

        $action = new Action();
        $action->partner_id = $partner->id;
        $action->status = Action::STATUS_ENABLED;
        $action->type = Action::TYPE_ORDER_CASHBACK;
        $action->value = 10;
        $action->value_type = Action::VALUE_TYPE_PERCENT;
        $action->is_system = false;
        $action->save();

        $action = new Action();
        $action->partner_id = $partner->id;
        $action->status = Action::STATUS_ENABLED;
        $action->type = Action::TYPE_ORDER_REFERRAL;
        $action->value = 5;
        $action->value_type = Action::VALUE_TYPE_PERCENT;
        $action->config = [
            Action::CONFIG_REFERRAL_REWARD_LIFETIME => 7 * 24 * 3600,
            Action::CONFIG_REFERRAL_REWARD_VALUE => 5,
            Action::CONFIG_REFERRAL_REWARD_VALUE_TYPE => Action::VALUE_TYPE_PERCENT,
        ];
        $action->is_system = false;
        $action->save();

        $action = new Action();
        $action->partner_id = $partner->id;
        $action->status = Action::STATUS_ENABLED;
        $action->type = Action::TYPE_JOIN_FB;
        $action->value = 50;
        $action->value_type = Action::VALUE_TYPE_FIXED;
        $action->config = [
            'url' => 'https://www.facebook.com/Giftd',
        ];
        $action->is_system = false;
        $action->save();

        if (\HLanguage::LANGUAGE_RU == $partner->default_language) {
            $action = new Action();
            $action->partner_id = $partner->id;
            $action->status = Action::STATUS_ENABLED;
            $action->type = Action::TYPE_JOIN_VK;
            $action->value = 50;
            $action->value_type = Action::VALUE_TYPE_FIXED;
            $action->config = [
                'url' => 'https://vk.com/giftd',
            ];
            $action->is_system = false;
            $action->save();
        }

        $action = new Action();
        $action->partner_id = $partner->id;
        $action->status = Action::STATUS_ENABLED;
        $action->type = Action::TYPE_SHARE_FB;
        $action->value = 100;
        $action->value_type = Action::VALUE_TYPE_FIXED;
        $action->config = [];
        $action->is_system = false;
        $action->save();

        if (\HLanguage::LANGUAGE_RU == $partner->default_language) {
            $action = new Action();
            $action->partner_id = $partner->id;
            $action->status = Action::STATUS_ENABLED;
            $action->type = Action::TYPE_SHARE_VK;
            $action->value = 100;
            $action->value_type = Action::VALUE_TYPE_FIXED;
            $action->config = [];
            $action->is_system = false;
            $action->save();
        }
    }

    public function createDefaultRewards(Partner $partner, $cheaper = false)
    {
        $reward = new Reward();
        $reward->partner_id = $partner->id;
        $reward->status = Reward::STATUS_ENABLED;
        $reward->type = Reward::TYPE_GIFTD_DISCOUNT;
        $reward->value = 5;
        $reward->value_type = Reward::VALUE_TYPE_PERCENT;
        $reward->price = 200 * $partner->money_to_points_multiplier;
        $reward->save();

        $reward = new Reward();
        $reward->partner_id = $partner->id;
        $reward->status = Reward::STATUS_ENABLED;
        $reward->type = Reward::TYPE_GIFTD_DISCOUNT;
        $reward->value = 10;
        $reward->value_type = Reward::VALUE_TYPE_PERCENT;
        $reward->price = 300 * $partner->money_to_points_multiplier;
        $reward->save();

        $reward = new Reward();
        $reward->partner_id = $partner->id;
        $reward->status = Reward::STATUS_ENABLED;
        $reward->type = Reward::TYPE_GIFTD_DISCOUNT;
        $reward->value = \HAmount::toLocalCurrency(300, $partner->default_language);
        $reward->value_type = Reward::VALUE_TYPE_FIXED;
        $reward->config = [
            Reward::CONFIG_MIN_AMOUNT_TOTAL => \HAmount::toLocalCurrency(2000, $partner->default_language),
        ];
        $reward->price = 400 * $partner->money_to_points_multiplier;
        $reward->save();

        $reward = new Reward();
        $reward->partner_id = $partner->id;
        $reward->status = Reward::STATUS_ENABLED;
        $reward->type = Reward::TYPE_GIFTD_DISCOUNT;
        $reward->value = \HAmount::toLocalCurrency(1000, $partner->default_language);
        $reward->value_type = Reward::VALUE_TYPE_FIXED;
        $reward->config = [
            Reward::CONFIG_MIN_AMOUNT_TOTAL => \HAmount::toLocalCurrency(5000, $partner->default_language),
        ];
        $reward->price = 500 * $partner->money_to_points_multiplier;
        $reward->save();

        $reward = new Reward();
        $reward->partner_id = $partner->id;
        $reward->status = Reward::STATUS_ENABLED;
        $reward->type = Reward::TYPE_GIFTD_DISCOUNT;
        $reward->value = \HAmount::toLocalCurrency(1000, $partner->default_language);
        $reward->value_type = Reward::VALUE_TYPE_FIXED;
        $reward->price = 2000 * $partner->money_to_points_multiplier;
        $reward->save();

        $reward = new Reward();
        $reward->partner_id = $partner->id;
        $reward->status = Reward::STATUS_ENABLED;
        $reward->type = Reward::TYPE_GIFTD_DISCOUNT;
        $reward->value = \HAmount::toLocalCurrency(2000, $partner->default_language);
        $reward->value_type = Reward::VALUE_TYPE_FIXED;
        $reward->price = 3000 * $partner->money_to_points_multiplier;
        $reward->save();

        $reward = new Reward();
        $reward->partner_id = $partner->id;
        $reward->status = Reward::STATUS_ENABLED;
        $reward->type = Reward::TYPE_GIFTD_DISCOUNT;
        $reward->value = \HAmount::toLocalCurrency(3000, $partner->default_language);
        $reward->value_type = Reward::VALUE_TYPE_FIXED;
        $reward->price = 4000 * $partner->money_to_points_multiplier;
        $reward->save();

        if ($cheaper) {
            $reward = new Reward();
            $reward->partner_id = $partner->id;
            $reward->status = Reward::STATUS_ENABLED;
            $reward->type = Reward::TYPE_GIFTD_DISCOUNT;
            $reward->value = 1;
            $reward->value_type = Reward::VALUE_TYPE_PERCENT;
            $reward->price = 50 * $partner->money_to_points_multiplier;
            $reward->save();
        }
    }

    public function createPayoutReward(Partner $partner): Reward
    {
        $reward = new Reward();
        $reward->partner_id = $partner->id;
        $reward->status = Reward::STATUS_ENABLED;
        $reward->type = Reward::TYPE_BITREWARDS_PAYOUT;
        $reward->saveOrFail();

        return $reward;
    }

    public function getEmbeddedUrl(Partner $partner, $url = null)
    {
        if (!$partner->url) {
            return $url;
        }

        return \HUrl::addParams($partner->url, ['_show-gcrm' => $url ?: 1]);
    }

    public function getEmbeddedUrlAutologin(User $user, $route = 'client.index')
    {
        return app(PartnerService::class)->getEmbeddedUrl(
            $user->partner,
            routePartner(
                $user->partner,
                $route,
                [\HApp::PARAM_AUTOLOGIN_TOKEN => app(UserService::class)->getAutologinToken($user)]
            )
        );
    }

    public function getUrlAutologin(User $user, $route = 'client.index')
    {
        return routePartner(
            $user->partner,
            $route,
            [\HApp::PARAM_AUTOLOGIN_TOKEN => app(UserService::class)->getAutologinToken($user)]
        );
    }

    public function getSignupUrl(Partner $partner)
    {
        return route($partner->isAuthMethodEmail() ? 'client.signupByEmail' : 'client.signupByPhone', ['partner' => $partner->key]);
    }

    public function askGiftdToPushPartnerToCrm($partner_code)
    {
        $apiClient = new ApiClient(
            config('giftd.admin.user_id'),
            config('giftd.admin.api_key')
        );

        $apiClient->queryCrm('partner/pushPartnerToCrm', ['partner_code' => $partner_code]);
    }

    /**
     * @return Partner
     */
    public function getTestPartner()
    {
        return Partner::orderBy('id')->first();
    }

    /**
     * @return Partner
     */
    public function changeLanguage(Partner $partner, $default_language)
    {
        $partner->default_language = $default_language;
        $partner->save();

        return $partner;
    }

    /**
     * @return Partner
     */
    public function changeCurrency(Partner $partner, $currency)
    {
        $partner->currency = $currency;
        $partner->save();

        return $partner;
    }

    public function getBitWithdrawMaxAmount(Partner $partner, ?User $user = null)
    {
        return $user ? $user->balance : $partner->getBitWithdrawMinAmount() * 2;
    }

    public function findByEthereumAddress(string $ethereumAddress): ?Partner
    {
        return Partner::model()
            ->where('eth_address', '=', \HStr::normalizeEthNumber($ethereumAddress))
            ->first();
    }

    public function getByGiftdId(int $giftdId): ?Partner
    {
        return Partner::whereGiftdId($giftdId)->first();
    }

    public function giveCustomBonusToAllUsers(Partner $partner, $points, $comment = '', $onlyConfirmedEmails = true, $preventNotification = false)
    {
        ob_start();

        $randomString = str_random(10);
        $tag = "bulk-bonus-giving-{$partner->id}-{$randomString}";

        $query = User::model()
            ->where(
                'partner_id', $partner->id
            )
            ->whereNotNull('email')
            ->orderBy('id');

        if ($onlyConfirmedEmails) {
            $query->whereNotNull('email_confirmed_at');
        }

        $query->chunk(100, function ($users) use ($points, $comment, $tag) {
            foreach ($users as $user) {
                \HMisc::echoIfInConsole("Giving bonus of {$points} points to user {$user->id}, email = {$user->email}, phone = {$user->phone} \n");

                app(UserService::class)->giveCustomBonusToUser(
                    new CustomBonusData($user, $points, \Auth::user(), null, null, $comment, $tag)
                );
            }
        });

        if (!$preventNotification) {
            if (\App::isLocal()) {
                $notificationEmails = self::DEBUG_EMAIL;
            } else {
                $notificationEmails = self::MASS_AWARD_NOTIFICATION_EMAILS;
                $notificationEmails[] = $partner->email;
            }

            $text = __('The GIFTD administrator has added %points% points to all users registered in the "%title%" loyalty program. If the charge occurred without your request, please, contact GIFTD support via email support@giftd.tech immediately!', ['title' => $partner->title, 'points' => $points]);
            $text .= "\n\n______________\n\nDebug info:\n".ob_get_clean();

            if ($notificationEmails) {
                foreach ($notificationEmails as $email) {
                    \Mail::raw($text, function ($message) use ($email) {
                        $message->subject(_('Mass award notification warning'));
                        $message->to($email);
                    });
                }
            }
        }
    }

    public function calculateFiatWithdrawFeeAmount(Partner $partner, float $amount): float
    {
        $feeType = $partner->getFiatWithdrawFeeType();

        switch ($feeType) {
            case Partner::BIT_WITHDRAWAL_FEE_TYPE_PERCENT:
                return round($amount / 100 * $partner->getFiatWithdrawFee(), 2);

            case Partner::BIT_WITHDRAWAL_FEE_TYPE_FIXED:
                return $partner->getFiatWithdrawFee();

            case null:
                return 0;
        }

        throw new \DomainException("Fee type '{$feeType}' not implemented");
    }

    public function enableFiatReferral(Partner $partner): void
    {
        $fiatReferralReward = $this->rewardService->getFiatWithdrawReward($partner);

        if (!$fiatReferralReward) {
            $fiatReferralReward = $this->rewardService->createFiatReferralReward($partner);
        }

        if (!$fiatReferralReward->isEnabled()) {
            $fiatReferralReward->enable()->save();
        }
    }

    public function disableFiatReferral(Partner $partner): void
    {
        $fiatReferralReward = $this->rewardService->getFiatWithdrawReward($partner);

        if ($fiatReferralReward && $fiatReferralReward->isEnabled()) {
            $fiatReferralReward->disable()->save();
        }
    }

    public function getNewRegistrationCount(Partner $partner, ?\DateTime $from = null, ?\DateTime $to = null): int
    {
        $query = $partner->users();

        if ($from) {
            $query->where('created_at', '>', $from);
        }

        if ($to) {
            $query->where('created_at', '<=', $to);
        }

        return $query->count();
    }

    public function getTotalWithdrawAmount(Partner $partner): float
    {
        $amountField = Transaction::DATA_FIAT_WITHDRAW_AMOUNT;
        $texField = Transaction::DATA_FIAT_WITHDRAW_FEE;
        $withdrawAmount = (float) $partner
            ->transactions()
            ->select(\DB::raw("COALESCE(SUM((data->>'$amountField')::float + (data->>'$texField')::float), 0) as totalAmount"))
            ->join('rewards', 'transactions.reward_id', 'rewards.id')
            ->where('rewards.type', Reward::TYPE_FIAT_WITHDRAW)
            ->where('transactions.status', Transaction::STATUS_CONFIRMED)
            ->first()
            ->totalAmount;

        return (float) abs($withdrawAmount);
    }

    public function calculateBalance(Partner $partner): float
    {
        $depositAmount = (float) $partner
            ->deposits()
            ->where('status', PartnerDeposit::STATUS_CONFIRMED)
            ->sum('amount');

        $withdrawAmount = $this->getTotalWithdrawAmount($partner);

        return $depositAmount - $withdrawAmount;
    }

    public function updateBalance(Partner $partner): void
    {
        $partner->balance = $this->calculateBalance($partner);
        $partner->saveOrFail();
    }

    public function getAverageChequeIncrease(Partner $partner, ?int $range = null, bool $cached = true)
    {
        $metricObject = new class() {
            use DefaultRangesTrait;
        };
        $range = $range ?? $metricObject->getDefaultRange();
        $cacheKey = "average-cheque-increase:{$partner->id}:${range}";

        if ($cached && cache()->has($cacheKey)) {
            return (float) cache()->get($cacheKey, 0);
        }

        $apiClient = app(ApiClient::class)->make($partner);
        $averageChequeIncrease = $apiClient->getReportData(
            Carbon::now()->subDays($range ?? $metricObject->getDefaultRange()),
            Carbon::now()
        )->averageChequeIncrease ?? 0;

        cache()->set($cacheKey, $averageChequeIncrease, 90000); //cache for one day + 1 hour

        return $averageChequeIncrease;
    }
}
