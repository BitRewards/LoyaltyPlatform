<?php

/**
 * HHttp helper class.
 *
 * Utility class to work with HTTP erroor classes.
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 *
 * @see http://www.ramirezcobos.com/
 * @see http://www.2amigos.us/
 *
 * @copyright 2013 2amigOS! Consultation Group LLC
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class HHttp
{
    const REQUEST_OK = 200;
    const ERROR_BADREQUEST = 400;
    const ERROR_UNAUTHORIZED = 401;
    const ERROR_REQUESTFAILED = 402;
    const ERROR_FORBIDDEN = 403;
    const ERROR_NOTFOUND = 404;
    const ERROR_INVALIDREQUEST = 405;
    const ERROR_INTERNAL = 500;
    const ERROR_INTERNAL_500 = 500;
    const ERROR_INTERNAL_502 = 502;
    const ERROR_INTERNAL_503 = 503;
    const ERROR_INTERNAL_504 = 504;

    public static $httpStatusMessage = array(
        self::REQUEST_OK => 'OK - Everything worked as expected.',
        self::ERROR_BADREQUEST => 'Bad Request - Often missing a required parameter.',
        self::ERROR_UNAUTHORIZED => 'Unauthorized - Invalid API key.',
        self::ERROR_REQUESTFAILED => 'Request Failed - Parameters were valid but request failed.',
        self::ERROR_NOTFOUND => "Not Found - The requested item doesn't exist.",
        self::ERROR_INVALIDREQUEST => 'Request type - Method not allowed.',
        self::ERROR_INTERNAL => "Server errors - something went wrong on Api's end.",
        self::ERROR_INTERNAL_500 => "Server errors - something went wrong on Api's end.",
        self::ERROR_INTERNAL_502 => "Server errors - something went wrong on Api's end.",
        self::ERROR_INTERNAL_503 => "Server errors - something went wrong on Api's end.",
        self::ERROR_INTERNAL_504 => 'Server errors - gateway timeout.',
    );

    public static function sendHttpResponseCode($code)
    {
        switch ($code) {
            case 100:
                $text = 'Continue';

                break;

            case 101:
                $text = 'Switching Protocols';

                break;

            case 200:
                $text = 'OK';

                break;

            case 201:
                $text = 'Created';

                break;

            case 202:
                $text = 'Accepted';

                break;

            case 203:
                $text = 'Non-Authoritative Information';

                break;

            case 204:
                $text = 'No Content';

                break;

            case 205:
                $text = 'Reset Content';

                break;

            case 206:
                $text = 'Partial Content';

                break;

            case 300:
                $text = 'Multiple Choices';

                break;

            case 301:
                $text = 'Moved Permanently';

                break;

            case 302:
                $text = 'Moved Temporarily';

                break;

            case 303:
                $text = 'See Other';

                break;

            case 304:
                $text = 'Not Modified';

                break;

            case 305:
                $text = 'Use Proxy';

                break;

            case 400:
                $text = 'Bad Request';

                break;

            case 401:
                $text = 'Unauthorized';

                break;

            case 402:
                $text = 'Payment Required';

                break;

            case 403:
                $text = 'Forbidden';

                break;

            case 404:
                $text = 'Not Found';

                break;

            case 405:
                $text = 'Method Not Allowed';

                break;

            case 406:
                $text = 'Not Acceptable';

                break;

            case 407:
                $text = 'Proxy Authentication Required';

                break;

            case 408:
                $text = 'Request Time-out';

                break;

            case 409:
                $text = 'Conflict';

                break;

            case 410:
                $text = 'Gone';

                break;

            case 411:
                $text = 'Length Required';

                break;

            case 412:
                $text = 'Precondition Failed';

                break;

            case 413:
                $text = 'Request Entity Too Large';

                break;

            case 414:
                $text = 'Request-URI Too Large';

                break;

            case 415:
                $text = 'Unsupported Media Type';

                break;

            case 500:
                $text = 'Internal Server Error';

                break;

            case 501:
                $text = 'Not Implemented';

                break;

            case 502:
                $text = 'Bad Gateway';

                break;

            case 503:
                $text = 'Service Unavailable';

                break;

            case 504:
                $text = 'Gateway Time-out';

                break;

            case 505:
                $text = 'HTTP Version not supported';

                break;

            default:
                throw new Exception('Unknown http status code "'.htmlentities($code).'"');
        }

        header('HTTP/1.1 '.$code.' '.$text);
    }

    /*
     * @params:Error code
     * @return:The message corresponds to error code
     */
    public static function getErrorMessage($errorCode)
    {
        return self::$httpStatusMessage[$errorCode];
    }

    /**
     * @param $method
     * @param $url
     * @param array      $data
     * @param array      $headers
     * @param bool|false $mobile
     * @param bool|false $httpAuth
     *
     * @return string
     *
     * @throws Exception
     */
    public static function doRequest($method, $url, $data = [], $headers = [], $mobile = false, $httpAuth = false, $timeout = false)
    {
        if ($headers) {
            $processedHeaders = [];

            foreach ($headers as $key => $value) {
                $processedHeaders[] = is_string($key) ? "$key: $value" : $value;
            }
            $headers = $processedHeaders;
        }
        $method = strtoupper($method);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($httpAuth) {
            curl_setopt($ch, CURLOPT_USERPWD, $httpAuth);
        }

        curl_setopt($ch, CURLOPT_USERAGENT,
            $mobile ?
                'Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_0 like Mac OS X; en-us) AppleWebKit/532.9 (KHTML, like Gecko) Version/4.0.5 Mobile/8A293 Safari/6531.22.7' :
                'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36');

        $paramsStr = '—';

        if ('GET' != $method) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $paramsStr = (is_array($data) ? http_build_query($data) : $data));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }

        if ($timeout) {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $timeout);
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
        }

        $result = curl_exec($ch);
        $status = (string) curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (!$status || '2' != $status[0]) {
            throw new Exception("HTTP $method to $url failed: HTTP ".$status.' ('.curl_error($ch)."), params: $paramsStr, response: ".$result);
        }

        return $result;
    }

    public static function doPost($url, $data = [], $headers = [], $mobile = false)
    {
        return static::doRequest('POST', $url, $data, $headers, $mobile, false);
    }

    public static function doJson($method, $url, $data = [], $mobile = false)
    {
        $headers = ['Content-Type: application/json'];

        if (!empty($data)) {
            $data = json_encode($data);
            $headers[] = 'Content-Length: '.strlen($data);
        }

        return json_decode(static::doRequest($method, $url, $data, $headers, $mobile, false));
    }

    public static function doGetJson($url, $data = [], $mobile = false)
    {
        return static::doJson('GET', $url, $data, $mobile);
    }

    public static function doPostJson($url, $data = [], $mobile = false)
    {
        return static::doJson('POST', $url, $data, $mobile);
    }

    public static function doPutJson($url, $data = [], $mobile = false)
    {
        return static::doJson('PUT', $url, $data, $mobile);
    }

    public static function doDelete($url, $data = [], $headers = [], $mobile = false)
    {
        return static::doRequest('DELETE', $url, $data, $headers, $mobile, false);
    }

    public static function doGet($url, $data = [], $headers = [], $mobile = false, $httpAuth = false, $timeout = false)
    {
        return static::doRequest('GET', $url, $data, $headers, $mobile, $httpAuth, $timeout);
    }

    public static function doGetMobile($url)
    {
        return static::doGet($url, [], [], true);
    }

    public static function downloadFile($filePath, $fileName = null)
    {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.(isset($fileName) ? $fileName : basename($filePath)).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        readfile($filePath);
    }
}
