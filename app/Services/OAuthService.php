<?php

namespace App\Services;

use App\DTO\SocialNetworkProfile;
use Illuminate\Http\Request;
use App\Models\Partner;
use Abraham\TwitterOAuth\TwitterOAuth;

class OAuthService
{
    const VK_SOCIAL_NETWORK = 'vk';
    const FB_SOCIAL_NETWORK = 'fb';
    const GOOGLE_SOCIAL_NETWORK = 'google';
    const TWITTER_SOCIAL_NETWORK = 'twitter';

    public function getSocialNetworkProfile(Request $request)
    {
        if ($code = $request->input('code')) {
            return $this->getSocialNetworkProfileUsingOAuth2($request);
        } elseif ($oauthToken = $request->input('oauth_token')) {
            return $this->getSocialNetworkProfileUsingOAuth1($request);
        } else {
            return null;
        }
    }

    public function url($socialNetwork, $partner)
    {
        switch ($socialNetwork) {
            case self::VK_SOCIAL_NETWORK:
            case self::FB_SOCIAL_NETWORK:
            case self::GOOGLE_SOCIAL_NETWORK:
                return $this->getOAuth2Url($socialNetwork, $partner);

            case self::TWITTER_SOCIAL_NETWORK:
                return $this->getOAuth1Url($partner);

            default:
                return routePartner($partner, 'client.index');
        }
    }

    public function isValidSocialNetwork($socialNetwork)
    {
        switch ($socialNetwork) {
            case self::VK_SOCIAL_NETWORK:
            case self::FB_SOCIAL_NETWORK:
            case self::GOOGLE_SOCIAL_NETWORK:
            case self::TWITTER_SOCIAL_NETWORK:
                return true;

            default:
                return false;
        }
    }

    private function useBitrewardsApp(Partner $partner)
    {
        return $partner->isBitrewardsEnabled();
    }

    private function buildSocialNetworkProfile($code, $socialNetwork, $partnerKey, $isBitrewardsEnabled = false)
    {
        $socialNetworkKey = $socialNetwork;

        if (true === $isBitrewardsEnabled && in_array($socialNetworkKey, ['fb', 'google'])) {
            $socialNetworkKey .= '_bitrewards';
        }

        $response = json_decode(\HHttp::doPost(config("$socialNetworkKey.access_token_url"), [
            'code' => $code,
            'client_id' => config("$socialNetworkKey.client_id"),
            'client_secret' => config("$socialNetworkKey.client_secret"),
            'redirect_uri' => \Request::getSchemeAndHttpHost().config('oauth.redirect_uri'),
            'grant_type' => 'authorization_code',
        ]));

        $profile = $this->grabSocialNetworkProfile($response, $socialNetwork);
        $profile->partnerKey = $partnerKey;

        return $profile;
    }

    private function grabSocialNetworkProfile($response, $socialNetwork)
    {
        switch ($socialNetwork) {
            case self::VK_SOCIAL_NETWORK:
                return $this->grabVkProfile($response);

            case self::FB_SOCIAL_NETWORK:
                return $this->grabFbProfile($response->access_token);

            case self::GOOGLE_SOCIAL_NETWORK:
                return $this->grabGoogleProfile($response);
        }
    }

    /**
     * @param $response
     *
     * @return SocialNetworkProfile
     */
    private function grabVkProfile($response): SocialNetworkProfile
    {
        $profile = \HHttp::doJson('GET', \HUrl::addParams('https://api.vk.com/method/users.get', [
            'access_token' => $response->access_token,
            'fields' => 'photo_200,email,first_name,last_name,id',
            'v' => '5.95',
        ]))->response[0];

        $socialNetworkProfile = new SocialNetworkProfile();
        $socialNetworkProfile->name = iso($profile->first_name).(iso($profile->last_name) ? (' '.$profile->last_name) : '');
        $socialNetworkProfile->picture = iso($profile->photo_200);
        $socialNetworkProfile->email = iso($response->email);
        $socialNetworkProfile->socialNetwork = self::VK_SOCIAL_NETWORK;
        $socialNetworkProfile->socialNetworkId = iso($profile->id);

        return $socialNetworkProfile;
    }

    /**
     * @param $fbAccessToken
     *
     * @return SocialNetworkProfile
     */
    public function grabFbProfile($fbAccessToken): SocialNetworkProfile
    {
        $profile = \HHttp::doJson('GET', \HUrl::addParams(config('fb.api_url').'me', [
            'access_token' => $fbAccessToken,
            'fields' => 'name, picture.width(300).height(300), email',
        ]));

        $socialNetworkProfile = new SocialNetworkProfile();
        $socialNetworkProfile->name = iso($profile->name);
        $socialNetworkProfile->picture = iso($profile->picture->data->url);
        $socialNetworkProfile->email = iso($profile->email);
        $socialNetworkProfile->socialNetwork = self::FB_SOCIAL_NETWORK;
        $socialNetworkProfile->socialNetworkId = iso($profile->id);

        return $socialNetworkProfile;
    }

    /**
     * @param $response
     *
     * @return SocialNetworkProfile
     */
    private function grabGoogleProfile($response): SocialNetworkProfile
    {
        // Unsafe without signature checking
        // Parsing JWT token
        // $parts = explode('.', $response->id_token);
        // $payload = iso($parts[1]);
        // $payload = base64_decode($payload);
        // $payload = json_decode($payload);

        // return [
        //     'name' => $payload->name,
        //     'picture' => $payload->picture,
        //     'email' => $payload->email,
        // ];

        $profile = \HHttp::doJson('GET', \HUrl::addParams(config('google.api_url').'userinfo', [
            'access_token' => $response->access_token,
        ]));

        $socialNetworkProfile = new SocialNetworkProfile();
        $socialNetworkProfile->name = iso($profile->name);
        $socialNetworkProfile->picture = iso($profile->picture);
        $socialNetworkProfile->email = iso($profile->email);
        $socialNetworkProfile->socialNetwork = self::GOOGLE_SOCIAL_NETWORK;
        $socialNetworkProfile->socialNetworkId = iso($profile->id);

        return $socialNetworkProfile;
    }

    /**
     * @param $user
     *
     * @return SocialNetworkProfile
     */
    private function grabTwitterProfile($user): SocialNetworkProfile
    {
        $socialNetworkProfile = new SocialNetworkProfile();
        $socialNetworkProfile->name = iso($user->name);
        $socialNetworkProfile->email = iso($user->email);
        $socialNetworkProfile->socialNetwork = self::TWITTER_SOCIAL_NETWORK;
        $socialNetworkProfile->socialNetworkId = iso($user->id);
        $socialNetworkProfile->partnerKey = session()->get('twitter_partner_key');

        $picture = '';

        if (isset($user->profile_image_url)) {
            $picture = str_replace('_normal', '', $user->profile_image_url);
        }
        $socialNetworkProfile->picture = $picture;

        return $socialNetworkProfile;
    }

    // vk, fb, google
    private function getSocialNetworkProfileUsingOAuth2($request)
    {
        $parts = explode('.', $request->input('state'));

        if (2 == count($parts)) {
            list($partnerKey, $socialNetwork) = $parts;

            if ($this->isValidSocialNetwork($socialNetwork)) {
                $partner = Partner::model()->findByKey($partnerKey);

                if ($partner) {
                    return $this->buildSocialNetworkProfile($request->input('code'), $socialNetwork, $partnerKey, $this->useBitrewardsApp($partner));
                }
            }
        }
    }

    // twitter
    private function getSocialNetworkProfileUsingOAuth1($request)
    {
        $profile = null;

        if (session()->get('oauth_token') == $request->input('oauth_token')) {
            $connection = new TwitterOAuth(
                config('twitter.consumer_key'),
                config('twitter.consumer_secret'),
                session()->get('oauth_token'),
                session()->get('oauth_token_secret')
            );

            $accessToken = $connection->oauth('oauth/access_token', ['oauth_verifier' => $request->input('oauth_verifier')]);

            $connection = new TwitterOAuth(
                config('twitter.consumer_key'),
                config('twitter.consumer_secret'),
                $accessToken['oauth_token'],
                $accessToken['oauth_token_secret']
            );

            $user = $connection->get('account/verify_credentials', ['include_email' => 'true']);

            $profile = $this->grabTwitterProfile($user);
        }

        session()->forget('oauth_token');
        session()->forget('oauth_token_secret');
        session()->forget('twitter_partner_key');

        return $profile;
    }

    private function getOAuth2Url($socialNetwork, $partner)
    {
        $socialNetworkKey = $socialNetwork;

        if ($this->useBitrewardsApp($partner) && in_array($socialNetworkKey, ['fb', 'google'])) {
            $socialNetworkKey .= '_bitrewards';
        }

        return \HUrl::addParams(config("$socialNetworkKey.url"), [
            'client_id' => config("$socialNetworkKey.client_id"),
            'redirect_uri' => \Request::getSchemeAndHttpHost().config('oauth.redirect_uri'),
            'display' => config('oauth.display'),
            'response_type' => config('oauth.response_type'),
            'scope' => config("$socialNetworkKey.scope"),
            'state' => "{$partner->key}.{$socialNetwork}", ]
        );
    }

    private function getOAuth1Url($partner)
    {
        $connection = new TwitterOAuth(config('twitter.consumer_key'), config('twitter.consumer_secret'));

        $callbackUrl = \Request::getSchemeAndHttpHost().config('twitter.callback');

        $requestToken = $connection->oauth('oauth/request_token', ['oauth_callback' => $callbackUrl]);
        $oauthToken = $requestToken['oauth_token'];
        $oauthTokenSecret = $requestToken['oauth_token_secret'];

        session()->put('oauth_token', $oauthToken);
        session()->put('oauth_token_secret', $oauthTokenSecret);
        session()->put('twitter_partner_key', $partner->key);

        $url = $connection->url('oauth/authorize', ['oauth_token' => $oauthToken]);

        return $url;
    }
}
