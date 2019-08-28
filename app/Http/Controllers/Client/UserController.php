<?php
/**
 * UserController.php
 * Creator: lehadnk
 * Date: 04/09/2018.
 */

namespace App\Http\Controllers\Client;

use App\DTO\PartnerPageDataFactory;
use App\DTO\UserData;
use App\Http\Controllers\Controller;
use App\Http\Requests\InviteByEmail;
use App\Http\Requests\PartnerPageRequest;
use App\Http\Requests\Support;
use App\Mail\ReferralInvite;
use App\Mail\SupportMessage;
use App\Models\Partner;
use App\Models\User;
use App\Services\ReferralStatisticService;
use App\Services\UserService;
use App\Services\OAuthService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ResetPasswordByPhoneRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\CredentialValidationToken;
use App\Services\EmailService;
use App\Services\Persons\AuthManagerService;
use App\Services\Persons\PersonFinder;
use App\Services\Persons\PersonGenerator;
use App\Services\Persons\PersonMerger;
use App\Services\Persons\UserGenerator;
use App\Services\SmsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    use AuthorizesRequests;

    const TRANSACTIONS_PER_PAGE = 20;

    /**
     * @var PartnerPageDataFactory
     */
    private $partnerPageDataFactory;

    /**
     * @var AuthManagerService
     */
    private $authService;

    /**
     * @var UserGenerator
     */
    private $userGeneratorService;

    /**
     * @var PersonGenerator
     */
    private $personGeneratorService;

    /**
     * @var PersonFinder
     */
    private $personFinderService;

    /**
     * @var EmailService
     */
    private $emailService;

    /**
     * @var SmsService
     */
    private $smsService;

    /**
     * @var SmsService
     */
    private $personMergerService;

    /**
     * @var OAuthService
     */
    private $OAuthService;

    /**
     * @var ReferralStatisticService
     */
    private $referralStatisticService;

    public function __construct(
        PartnerPageDataFactory $partnerDataPageFactory,
        AuthManagerService $authService,
        UserGenerator $userGeneratorService,
        PersonGenerator $personGeneratorService,
        PersonFinder $personFinderService,
        EmailService $emailService,
        SmsService $smsService,
        PersonMerger $personMergerService,
        OAuthService $OAuthService,
        ReferralStatisticService $referralStatisticService
    ) {
        $this->partnerPageDataFactory = $partnerDataPageFactory;
        $this->authService = $authService;
        $this->userGeneratorService = $userGeneratorService;
        $this->personGeneratorService = $personGeneratorService;
        $this->personFinderService = $personFinderService;
        $this->emailService = $emailService;
        $this->smsService = $smsService;
        $this->personMergerService = $personMergerService;
        $this->OAuthService = $OAuthService;
        $this->referralStatisticService = $referralStatisticService;
    }

    public function setAppCookies()
    {
        setcookie('laravel_session', session()->getId(), time() + (60 * 60 * 24 * 30), '/', 'app.bitrewards.com');
        header('x-csrf-token: fix');

        return view('loyalty/set-app-cookies');
    }

    private function saveUtmTagsToCookies()
    {
        $tags = [
            'utm_content',
            'utm_medium',
            'utm_source',
            'utm_term',
            'utm_campaign',
        ];
        $utmData = [];

        foreach ($tags as $tag) {
            if (!empty($_GET[$tag])) {
                $utmData[$tag] = $_GET[$tag];
            }
        }

        if ($utmData) {
            setcookie('last_utm_data', \HJson::encode($tags), (time() + 3600 * 24 * 365), '/', null);
        }
    }

    private function getUtmTagsFromCookies()
    {
        if (isset($_COOKIE['last_utm_data'])) {
            return \HJson::decode($_COOKIE['last_utm_data']);
        } else {
            return [];
        }
    }

    private function fillDataAboutUtm(User $user)
    {
        if ($user->hasSomeUtmData()) {
            return;
        }

        $utmData = $this->getUtmTagsFromCookies();

        if (!$utmData) {
            return;
        }

        $mapping = [
            'utm_content' => User::DATA_UTM_CONTENT,
            'utm_term' => User::DATA_UTM_TERM,
            'utm_medium' => User::DATA_UTM_MEDIUM,
            'utm_campaign' => User::DATA_UTM_CAMPAIGN,
            'utm_source' => User::DATA_UTM_SOURCE,
        ];

        $newUserData = [];
        $finalData = [];

        foreach ($utmData as $key => $value) {
            $value = mb_strtolower(trim($value));

            if (isset($mapping[$key]) && $value) {
                $mappedDataKey = $mapping[$key];
                $newUserData[$mappedDataKey] = mb_substr($value, 0, 255);
            }
        }

        $existingData = $user->data;

        if (is_array($newUserData) && is_array($existingData)) {
            $finalData = array_replace_recursive($newUserData, $existingData);
        }

        $user->data = $finalData;
        $user->save();
    }

    public function index(PartnerPageRequest $request)
    {
        if ($user = \Auth::user()) {
            $this->fillDataAboutUtm($user);
        } else {
            $this->saveUtmTagsToCookies();
        }

        if (!$request->partner) {
            abort(404);
        }
        // TODO: Make view for unauthorized:
        if (!$user && $request->partner->isAvtocodPartner()) {
            //abort(403);
        }

        return view(
            'loyalty/index',
            [
                'partnerPage' => $this->partnerPageDataFactory->factory(
                    $request->partner,
                    \Auth::user(),
                    $request->get('title'),
                    [Input::get('tag', ''), '*']
                ),
            ]
        );
    }

    public function logout($partner)
    {
        if (\Auth::check()) {
            \Auth::logout();
        }

        return redirect(route('client.index', ['partner' => $partner->key]));
    }

    public function twitterOAuth(Request $request, Partner $partner)
    {
        $url = $this->OAuthService->url(OAuthService::TWITTER_SOCIAL_NETWORK, $partner);

        return redirect($url);
    }

    public function forgot(Request $request, Partner $partner)
    {
        if ($partner->isAuthMethodPhone()) {
            $this->validate($request, ['emailOrPhone' => 'required|regex:/^[\+\(\)\-0-9]{6,20}$/']);
            $phone = \HUser::normalizePhone($request->emailOrPhone, $partner->default_country);
            $person = $this->personFinderService->findByPhone($phone, $partner->partner_group_id);

            if ($person) {
                $this->smsService->confirmPersonPhone($person, $phone);
            }
        } else {
            $this->validate($request, ['emailOrPhone' => 'required|email']);
            $email = \HUser::normalizeEmail($request->emailOrPhone);

            $person = $this->personFinderService->findByEmail($email, $partner->partner_group_id);

            if ($person) {
                $this->emailService->sendPersonEmailConfirmation($partner, $person, $email);
            }
        }

        return jsonResponse('ok');
    }

    public function resetPasswordByPhone(ResetPasswordByPhoneRequest $request)
    {
        return jsonResponse('ok');
    }

    public function resetPasswordByEmail(ResetPasswordByPhoneRequest $request)
    {
        return jsonResponse('ok');
    }

    public function setNewPassword(ResetPasswordRequest $request)
    {
        $person = $this->personFinderService->findByEmailOrPhone($request->emailOrPhone, $request->partner->partner_group_id);

        if (!$person) {
            return jsonError(__('No such person'));
        }

        if (!$token = CredentialValidationToken::validateEmailOrPhone($request->emailOrPhone, $request->token)) {
            return jsonError(__('Invalid code from email or phone'));
        }

        $token->redeem();

        /**
         * Credential $credential.
         */
        $credential = $person->credentials->filter(function ($credential) use ($request) {
            return $credential->emailOrPhone === $request->email || $credential->phone == \HUser::normalizePhone($request->emailOrPhone, $request->partner->default_country);
        })->first();

        if ($credential) {
            $credential->setPassword($request->password);
            $credential->save();
        }

        \Auth::login($person->getUser($request->partner->id));

        return jsonRedirect(routePartner($request->partner, 'client.index'));
    }

    public function referralStatistic(Partner $partner, Request $request): JsonResponse
    {
        try {
            $request->validate([
                'from' => 'date',
                'to' => 'date|after:from',
            ]);

            $from = $request->get('from');

            if ($from) {
                $from = (new \DateTime($from))->modify('00:00:00');
            }

            $to = $request->get('to');

            if ($to) {
                $to = (new \DateTime($to))->modify('23:59:59');
            }

            $statisticData = $this
                ->referralStatisticService
                ->getReferralStatistic($partner, $from, $to, Auth::user());
        } catch (ValidationException $e) {
            return jsonError([
                'error' => $e->errors(),
            ]);
        } catch (\Exception $e) {
            return jsonError([
                'error' => __('Some error happened'),
            ]);
        }

        return jsonResponse($statisticData);
    }

    public function saveEmailFromExternalRequest(Request $request, Partner $partner): JsonResponse
    {
        header('Access-Control-Allow-Origin: *');

        $email = \HUser::normalizeEmail($request->get('email'));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return jsonError(['email' => 'Malformed email']);
        }

        $existingUser = User::model()->findByPartnerAndEmail($partner, $email);

        if ($existingUser) {
            return \jsonResponse('ok');
        }

        app(UserService::class)->createClient(UserData::make([
            'email' => $email,
            'signup_type' => User::SIGNUP_TYPE_UNSECURE_API,
            'utm_content' => $request->get('utm_content'),
            'utm_source' => $request->get('utm_source'),
            'utm_medium' => $request->get('utm_medium'),
            'utm_term' => $request->get('utm_term'),
            'utm_campaign' => $request->get('utm_campaign'),
        ]), $partner);

        return \jsonResponse('ok');
    }

    public function getTransactions($partner, $skip = 0)
    {
        if ($user = \Auth::user()) {
            $transactions = $user->transactions;

            return view('loyalty/_transactions', compact('partner', 'transactions'));
        }
    }

    public function getBalance(Partner $partner, $userKey, $callback = null)
    {
        $userData = app(UserService::class)->getBasicUserData($partner, $userKey);
        $balance = $userData['balance'] ?? null;

        if (null === $balance) {
            $balance = 'null';
        }

        if ($callback) {
            $response = response($callback."($balance);");
        } else {
            $response = response($balance);
        }

        return $response->header('Content-Type', 'text/javascript');
    }

    public function getBasicUserData(Partner $partner, $userKey, $callback = null)
    {
        $userData = app(UserService::class)->getBasicUserData(
            $partner,
            $userKey,
            $_GET['flush-cache'] ?? false
        );

        $userData = json_encode($userData);

        if ($callback) {
            $response = response($callback."($userData);");
        } else {
            $response = response($userData);
        }

        return $response->header('Content-Type', 'text/javascript');
    }

    public function support(Support $request)
    {
        $mail = new SupportMessage(
            $request->user(),
            $request->message,
            $request->email
        );

        \Mail::queue($mail);

        return jsonResponse(['email' => $request->email]);
    }

    public function invite(InviteByEmail $request)
    {
        foreach ($request->getEmailsArray() as $email) {
            $mail = new ReferralInvite(
                $request->user(),
                $email,
                $request->sender_name,
                $request->message
            );

            \Mail::queue($mail);
        }

        return jsonResponse(['email' => implode(', ', $request->getEmailsArray())]);
    }

    public function getConfirmationStatus()
    {
        $user = \Auth::user();
        $result = app(UserService::class)->getConfirmationStatus($user);

        return jsonResponse([
            'result' => $result,
        ]);
    }

    public function unsubscribe(Request $request, Partner $partner)
    {
        $user = \Auth::user();
        $user->is_unsubscribed = true;
        $user->save();

        return view(
            'loyalty/unsubscribed',
            compact(
                'user',
                'partner'
            )
        );
    }
}
