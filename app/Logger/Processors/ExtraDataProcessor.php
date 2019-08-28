<?php

namespace App\Logger\Processors;

class ExtraDataProcessor
{
    public function __invoke(array $data)
    {
        $extraData = [
            'Date' => date('d.m.Y H:i:s'),
            'URL' => \Request::fullUrl(),
            'IP' => $_SERVER['REMOTE_ADDR'] ?? null,
            '$_GET' => $_GET ?? null,
            '$_POST' => $_POST ?? null,
            'Session' => app()->has('session') ? session()->all() : [],
            'Cookies' => $_COOKIE ?? null,
            'User Agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'Referrer' => $_SERVER['HTTP_REFERER'] ?? null,
            'HTTP Request Method' => $_SERVER['REQUEST_METHOD'] ?? null,
        ];

        if ($user = \Auth::user()) {
            $extraData['Current user id'] = $user->getAuthIdentifier();
            $extraData['Current user email'] = $user->getEmail();
            $extraData['Current user phone'] = $user->getPhone();
            $extraData['Current user partner id'] = $user->getPartnerId();
            $extraData['Current user name'] = $user->getName();
        }

        if (isset($data['context'])) {
            $context = array_diff_key($data['context'], array_flip(['exception']));

            if (!empty($context)) {
                $extraData['Context'] = $context;
            }
        }

        $data['extra'] = $extraData;

        return $data;
    }
}
