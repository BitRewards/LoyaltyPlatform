<?php

class HUrl
{
    public static $PUBLIC_ESP = ['mail.ru', 'gmail.com', 'list.ru', 'yandex.ru', 'ya.ru', 'hotmail.com', 'list.ru', 'bk.ru', 'inbox.ru', 'rambler.ru', 'gmail.com', 'yahoo.com', 'hotmail.com', 'gmx.de', 'googlemail.com', 'mail.ru', 'web.de', 'live.com', 'aol.com', 'gmx.net', 'yandex.ru', 'me.com', 'msn.com', 'comcast.net', 'hushmail.com', 'yahoo.de', 'hotmail.co.uk', 'ymail.com', 'safe-mail.net', 'yahoo.co.uk', 'hotmail.de', 'qq.com', 'mac.com', 'yandex.com'];

    public static function addLineBreaks($url)
    {
        return strtr($url, ['?' => "?\n", '&' => "&\n"]);
    }

    public static function beautify($url)
    {
        return str_replace('www.', '', rtrim(substr(
            self::decodeIdn($url),
            ($slashes = strpos($url, '//')) ? ($slashes + 2) : 0
        ), '/ '));
    }

    public static function normalize($url)
    {
        if (!$url) {
            return $url;
        }
        $url = trim($url);

        if (false === strpos($url, '//')) {
            $url = 'http://'.$url;
        }

        return $url;
    }

    public static function isFull($url)
    {
        return 0 === strpos($url, 'http://') || 0 === strpos($url, 'https://');
    }

    public static function removeParams($url, $paramNames)
    {
        $paramNames = (array) $paramNames;

        foreach ($paramNames as $name) {
            $url = preg_replace("/$name=[^$&]*/i", '', $url);
        }

        return $url;
    }

    public static function getAllParams($url)
    {
        $parts = parse_url($url);

        if (!iso($parts['query'])) {
            return [];
        }

        parse_str($parts['query'], $result);

        return $result;
    }

    public static function getParam($url, $name)
    {
        preg_match("/$name=([^$&]*)/i", $url, $matches);

        return iso($matches[1]);
    }

    public static function addParams($url, array $newParams)
    {
        $parts = parse_url($url);

        if (isset($parts['host'])) {
            $url = iso($parts['scheme'], 'http').'://'.$parts['host'];
        } else {
            $url = '';
        }

        $url .= iso($parts['path'], '/');

        $params = [];

        if (isset($parts['query'])) {
            parse_str($parts['query'], $params);
        }

        $params = array_merge($params, $newParams);

        if ($params) {
            $url .= '?'.http_build_query($params);
        }

        if (isset($parts['fragment'])) {
            $url .= '#'.$parts['fragment'];
        }

        return $url;
    }

    public static function stripScheme($url)
    {
        return strtr($url, ['https://' => '', 'http://' => '']);
    }

    public static function getRoot($url)
    {
        if (!$url) {
            return null;
        }
        $url = trim($url, ' /#?');

        if (!$url) {
            return null;
        }
        $parts = parse_url($url);

        return ($parts && iso($parts['host']) && iso($parts['scheme'])) ?
            ($parts['scheme'].'://'.$parts['host']) :
            null;
    }

    public static function getHost($url)
    {
        if (!$url) {
            return null;
        }
        $parts = parse_url($url);
        $host = iso($parts['host']);

        if ($host && 0 === strpos($host, 'www.')) {
            $host = str_replace('www.', '', $host);
        }

        return $host;
    }

    public static function getPath($url)
    {
        if (!$url) {
            return null;
        }
        $parts = parse_url($url);

        return iso($parts['path']);
    }

    public static function extractEmailImportantPart($email)
    {
        $parts = explode('@', $email);
        $host = iso($parts[1]);

        if (!$host || in_array(strtolower($host), self::$PUBLIC_ESP)) {
            return $parts[0];
        } else {
            return $host;
        }
    }

    public static function replacePath($url, $newPath)
    {
        if ($newPath && '/' == $newPath[0] && false === strpos($newPath, '..')) {
            $urlParts = parse_url($url);

            if (!$urlParts) {
                return $url;
            }

            return $shopUrl = iso($urlParts['scheme'], 'http').'://'.$urlParts['host'].$newPath;
        }

        return $url;
    }

    public static function decodeIdn($value)
    {
        if (false === strpos($value, 'xn--')) {
            return $value;
        }

        if (preg_match_all('/^(.*):\/\/([^\/]+)(.*)$/', $value, $matches)) {
            if (function_exists('idn_to_utf8')) {
                $value = $matches[1][0].'://'.idn_to_utf8($matches[2][0]).$matches[3][0];
            } else {
                // nothing to do :(
            }
        }

        return $value;
    }
}
