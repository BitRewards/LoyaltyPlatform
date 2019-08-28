<?php

namespace App\Services\Giftd;

use App\DTO\ApiClient\ReportData;
use App\DTO\ApiClient\ReportDataFactory;
use App\Models\Partner;
use Cache;
use Carbon\Carbon;
use HApp;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\Lock\Factory;

/**
 * @property LoggerInterface $logger
 */
class ApiClient implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private $userId;
    private $apiKey;
    private $baseUrl;

    private $clientIp;

    private $remoteDebug = false;

    const RESPONSE_TYPE_DATA = 'data';
    const RESPONSE_TYPE_ERROR = 'error';

    const ERROR_NETWORK_ERROR = 'networkError';
    const ERROR_TOKEN_NOT_FOUND = 'tokenNotFound';
    const ERROR_EXTERNAL_ID_NOT_FOUND = 'externalIdNotFound';
    const ERROR_DUPLICATE_EXTERNAL_ID = 'duplicateExternalId';
    const ERROR_TOKEN_ALREADY_USED = 'tokenAlreadyUsed';
    const ERROR_YOUR_ACCOUNT_IS_BANNED = 'yourAccountIsBanned';

    public function __construct($userId = null, $apiKey = null, LoggerInterface $logger = null)
    {
        $this->userId = $userId;
        $this->apiKey = $apiKey;
        $this->baseUrl = config('giftd.api_base_url');

        if (null === $logger) {
            $this->logger = app('giftd.api.log');
        }
    }

    public static function create(Partner $partner)
    {
        if (!$partner->isConnectedToGiftdApi()) {
            throw new ApiException("Partner #{$partner->id} does not have giftd_api_key or giftd_id, unable to create GIFTD API client!");
        }

        return new static($partner->giftd_user_id, $partner->giftd_api_key);
    }

    public function make(Partner $partner): self
    {
        return self::create($partner);
    }

    private function httpPostCurl($url, array $params)
    {
        $ch = curl_init($url);

        foreach ($params as $key => &$value) {
            if (null === $value) {
                $value = '';
            }
        }

        curl_setopt_array($ch, array(
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => http_build_query($params),
            CURLOPT_RETURNTRANSFER => 1,
        ));

        if ($this->usingLocalApi()) {
            curl_setopt_array($ch, array(
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
            ));
        }

        if ($this->isXDebug()) {
            curl_setopt($ch, CURLOPT_COOKIE, 'XDEBUG_SESSION=GIFTD_API');
        }

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new NetworkException("HTTP POST to $url failed: ".curl_error($ch));
        }

        return $result;
    }

    /**
     * Determine if we're using local API host.
     *
     * @return bool
     */
    public function usingLocalApi(): bool
    {
        return false !== strpos($this->baseUrl, '.tech-local');
    }

    protected function isXDebug(): bool
    {
        return $this->remoteDebug || !empty(
            ini_get('xdebug.remote_autostart')
            ?? $_COOKIE['XDEBUG_SESSION']
            ?? $_POST['XDEBUG_SESSION_START']
            ?? null
        );
    }

    private function httpPostFileGetContents($url, array $params)
    {
        $opts = array('http' => array(
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query($params),
            ),
        );

        if ($this->usingLocalApi()) {
            $opts['ssl'] = [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ];
        }

        $context = stream_context_create($opts);

        $result = @file_get_contents($url, false, $context);

        if (!$result) {
            throw new NetworkException("HTTP POST to $url failed or returned empty result");
        }

        return $result;
    }

    private function httpPost($url, array $params)
    {
        $this->logger->debug("Request: $url", $params);

        try {
            if (function_exists('curl_init')) {
                $rawResult = $this->httpPostCurl($url, $params);
            } else {
                $rawResult = $this->httpPostFileGetContents($url, $params);
            }

            if (!($result = json_decode($rawResult, true))) {
                $encodedParams = \HJson::encode($params);

                throw new ApiException("Giftd API returned malformed JSON, url = $url, params = $encodedParams, response = \n$rawResult");
            }
        } catch (\Exception $e) {
            $this->logger->error("Request failed: {$e->getMessage()}", $e->getTrace());

            throw $e;
        }

        $this->logger->debug("Response: $url", (array) $result);

        return $result;
    }

    public function queryCrm($method, $params = [])
    {
        $old = $this->baseUrl;
        $this->baseUrl = str_replace('v1', 'crm_v1', $this->baseUrl);
        $exception = $result = null;

        try {
            $result = $this->query($method, $params);
        } catch (\Exception $e) {
            $exception = $e;
        }

        $this->baseUrl = $old;

        if ($exception) {
            throw $exception;
        }

        return $result;
    }

    public function cacheCrmQuery($method, array $params = [], int $ttl = 60)
    {
        $paramsHash = sha1(serialize([
            'userId' => $this->userId,
            'apiKey' => $this->apiKey,
        ] + $params));
        $cacheKey = "apiClient:{$method}:{$paramsHash}";
        $lock = app(Factory::class)->createLock($cacheKey, $ttl);

        if ($lock->acquire(true)) {
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            $result = $this->queryCrm($method, $params);

            if ($ttl > 0) {
                Cache::set($cacheKey, $result, $ttl);
            }

            return $result;
        }

        throw new \RuntimeException('Lock resource failed');
    }

    public function withClientIp($ip)
    {
        $this->clientIp = $ip;

        return $this;
    }

    public function query($method, $params = array(), $suppressExceptions = false)
    {
        if ($this->clientIp) {
            $params['client_ip'] = $this->clientIp;
        }
        $params['signature'] = $this->calculateSignature($method, $params);
        $params['user_id'] = $this->userId;

        $result = $this->httpPost($this->baseUrl.$method, $params);

        if (empty($result['type'])) {
            throw new ApiException('Giftd API returned response without type field, unable to decode it');
        }

        if (!$suppressExceptions && $result['type'] == static::RESPONSE_TYPE_ERROR) {
            $this->throwException($result);
        }

        return $suppressExceptions ? $result : $result[static::RESPONSE_TYPE_DATA];
    }

    private function throwException(array $rawResponse)
    {
        throw new ApiException($rawResponse[static::RESPONSE_TYPE_DATA], $rawResponse['code']);
    }

    private function constructGiftCard(array $rawData, $token)
    {
        $card = new Card();

        foreach ($rawData as $key => $value) {
            $card->$key = $value;
        }

        if (!$card->token) {
            $card->token = $token;
        }

        if ($card->charge_details) {
            $chargeDetails = new ChargeDetails();

            foreach ($card->charge_details as $key => $value) {
                $chargeDetails->$key = $value;
            }
            $card->charge_details = $chargeDetails;
        }

        return $card;
    }

    /**
     * @param null  $token
     * @param null  $external_id
     * @param float $amountTotal
     *
     * @return Card|null
     *
     * @throws ApiException
     */
    public function check($token = null, $external_id = null, $amountTotal = null)
    {
        $response = $this->query('gift/check', array(
            'token' => $token,
            'external_id' => $external_id,
            'amount_total' => $amountTotal,
            'disable_bruteforce_protection' => true,
        ), true);

        switch ($response['type']) {
            case static::RESPONSE_TYPE_ERROR:
                switch ($response['code']) {
                    case static::ERROR_TOKEN_NOT_FOUND:
                    case static::ERROR_EXTERNAL_ID_NOT_FOUND:
                        return null;

                    default:
                        $this->throwException($response);
                }

                break;

            case static::RESPONSE_TYPE_DATA:
                return $this->constructGiftCard($response['data'], $token);

            default:
                throw new ApiException("Unknown response type {$response['type']}");
        }

        return null;
    }

    /**
     * @param $externalId
     *
     * @return Card|null
     *
     * @throws ApiException
     */
    public function checkByExternalId($externalId)
    {
        return $this->check(null, $externalId);
    }

    /**
     * @param $token
     * @param $amountTotal
     *
     * @return Card|null
     *
     * @throws ApiException
     */
    public function checkByToken($token, $amountTotal = null)
    {
        return $this->check($token, null, $amountTotal);
    }

    /**
     * @param $token
     * @param $amount
     * @param null $amountTotal
     * @param null $externalId
     * @param null $comment
     *
     * @return Card
     *
     * @throws ApiException
     */
    public function charge($token, $amount = null, $amountTotal = null, $externalId = null, $comment = null)
    {
        $result = $this->query('gift/charge', array(
            'token' => $token,
            'amount' => $amount,
            'amount_total' => $amountTotal,
            'external_id' => $externalId,
            'comment' => $comment,
        ));

        return $this->constructGiftCard($result, $token);
    }

    private function calculateSignature($method, array $params)
    {
        $signatureBase = $method.','.$this->userId.',';
        unset($params['user_id'], $params['signature'], $params['api_key']);
        ksort($params);

        foreach ($params as $key => $value) {
            $signatureBase .= $key.'='.(is_array($value) ? 'array' : $value).',';
        }

        $signatureBase .= $this->apiKey;

        return sha1($signatureBase);
    }

    public function getClicksCountForReferralLink(string $referralLink, \DateTime $from, \DateTime $to): int
    {
        $result = $this->queryCrm('referral/getClicksCountByReferralLink', [
            'url' => $referralLink,
            'from' => $from->getTimestamp(),
            'to' => $to->getTimestamp(),
        ]);

        return $result['count'] ?? 0;
    }

    public function getUniqueReferralsCountDailyStatistic(\DateTime $from, \DateTime $to)
    {
        return $this->queryCrm('referral/getUniqueReferralsCountDailyStatistic', [
            'from' => $from->getTimestamp(),
            'to' => $to->getTimestamp(),
        ]);
    }

    public function getPartnerSettings(): array
    {
        return $this->cacheCrmQuery('partner/settings', [], 5);
    }

    public function updatePartnerSettings(array $settings): void
    {
        $this->queryCrm('partner/updateSettings', [
            'settings' => $settings,
        ]);
    }

    public function getReportData(\DateTime $from, \DateTime $to): ReportData
    {
        $result = $this->cacheCrmQuery('partner/reportData', [
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
        ]);

        return app(ReportDataFactory::class)->create($result['data']);
    }

    protected function secondsLeftInCurrentDay(): int
    {
        if (!HApp::isProduction()) {
            return 0;
        }

        return Carbon::now()->modify('23:59:59')->diffInSeconds(Carbon::now());
    }

    public function getSentEmailsTrend(?\DateTime $from = null, ?\DateTime $to = null): array
    {
        return $this->cacheCrmQuery('partner/sentEmailsTrend', [
            'from' => $from ? $from->format('Y-m-d') : null,
            'to' => $to ? $to->format('Y-m-d') : null,
        ], $this->secondsLeftInCurrentDay())['data'] ?? [];
    }

    public function getContactsTrend(\DateTime $from, ?\DateTime $to = null): array
    {
        return $this->cacheCrmQuery('partner/contactsTrend', [
            'from' => $from ? $from->format('Y-m-d') : null,
            'to' => $to ? $to->format('Y-m-d') : null,
        ], $this->secondsLeftInCurrentDay())['data'] ?? [];
    }

    public function getWidgetPostCountTrend(\DateTime $from, ?\DateTime $to = null): ?array
    {
        return $this->cacheCrmQuery('partner/widgetPostCountTrend', [
            'from' => $from ? $from->format('Y-m-d') : null,
            'to' => $to ? $to->format('Y-m-d') : null,
        ], 10)['data'] ?? null;
    }
}
