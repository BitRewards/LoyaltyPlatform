<?php

use App\Services\PartnerService;

function iso(&$value, $default = null)
{
    return isset($value) ? $value : $default;
}

if (!function_exists('password_hash')) {
    function password_hash($password)
    {
        return crypt($password);
    }
    function password_verify($password, $hash)
    {
        return crypt($password, $hash) == $hash;
    }
}

function random_digit_code($length)
{
    $token = '';

    for ($i = 0; $i < $length; ++$i) {
        $token .= mt_rand(0, 9);
    }

    return $token;
}

function vdr($var)
{
    ob_start();
    var_dump($var);

    return ob_get_clean();
}

function mb_str_replace($needle, $replacement, $haystack)
{
    return implode($replacement, mb_split($needle, $haystack));
}

if (!function_exists('__')) {
    function __(...$args)
    {
        if (1 == count($args)) {
            return explode('##', _($args[0]))[0];
        }

        if (count($args) > 1) {
            if (!is_array($args[1])) {
                if (preg_match_all('/%[a-zA-Z_0-9]+%/', $args[0], $matches)) {
                    $temp = [];

                    foreach ($matches[0] as $i => $placeholder) {
                        $temp[$placeholder] = $args[$i + 1];
                    }
                    $args[1] = $temp;
                }
            }
        }

        if (is_array($args[1])) {
            $temp = [];

            foreach ($args[1] as $key => $value) {
                if ($key[0] >= 'a' && $key[0] <= 'z') {
                    $temp["%$key%"] = $value;
                } else {
                    $temp[$key] = $value;
                }
            }

            if (isset($temp['%count%'])) {
                $regex = '/{([^|]+)\|([^|]+)(\|([^}]+)|)}/';

                $count = $temp['%count%'];

                if (HLanguage::LANGUAGE_RU == HLanguage::getCurrent()) {
                    $form = 1 == $count % 10 && 11 != $count % 100 ? 1 : ($count % 10 >= 2 && $count % 10 <= 4 && ($count % 100 < 10 || $count % 100 >= 20) ? 2 : 4);
                } else {
                    $form = abs($count) > 1 ? 2 : 1;
                }
                $args[0] = preg_replace($regex, "\${$form}", _($args[0]));
            } else {
                $args[0] = _($args[0]);
            }
            $result = strtr($args[0], $temp);
        } else {
            $result = vsprintf(_($args[0]), array_slice($args, 1));
        }

        return explode('##', $result)[0];
    }
}

/**
 * Placeholder for <a> and </a> are "{" and "}".
 *
 * @param $text
 * @param $url
 * @param array $htmlOptions
 */
function __link($text, $url, array $htmlOptions = [])
{
    return __($text, [
        '{' => '<a '.Html::attributes(['href' => $url] + $htmlOptions).'>',
        '}' => '</a>',
    ]);
}

function json($value)
{
    return json_encode($value, JSON_HEX_TAG | JSON_HEX_AMP | JSON_PRETTY_PRINT);
}

function object_to_array($obj)
{
    if (is_object($obj)) {
        $obj = (array) $obj;
    }

    if (is_array($obj)) {
        $new = array();

        foreach ($obj as $key => $val) {
            $new[$key] = object_to_array($val);
        }
    } else {
        $new = $obj;
    }

    return $new;
}

function jsonResponse($data = null)
{
    return response()->json([
        'type' => 'data',
        'data' => $data,
    ]);
}

function jsonError($errors = [], $errorCode = null, $httpResponseCode = 400)
{
    $response = [
        'type' => 'error',
        'data' => $errors,
    ];

    if ($errorCode) {
        $response['code'] = (string) $errorCode;
    }

    return response()->json($response, $httpResponseCode);
}

function jsonRedirect($url)
{
    return response()->json([
        'type' => 'redirect',
        'data' => ['url' => $url],
    ]);
}

function routePartner(App\Models\Partner $partner, $route, $params = [], $absolute = true)
{
    return route($route, array_replace_recursive(['partner' => $partner->key], $params));
}

function routeEmbedded(App\Models\Partner $partner, $route, $params = [])
{
    if (!$partner->url) {
        return routePartner($partner, $route, $params);
    }
    $url = routePartner($partner, $route, $params, false);

    return app(PartnerService::class)->getEmbeddedUrl($partner, $url);
}
