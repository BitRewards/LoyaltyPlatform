<?php

namespace App\Exceptions;

use App\Models\Partner;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SentryContext
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * SentryContext constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param Request $request
     * @param array   $extra
     *
     * @return mixed
     */
    public static function capture(Request $request, array $extra = [])
    {
        return (new static($request))->process($extra);
    }

    /**
     * @param array $extra
     *
     * @return array
     */
    public function process(array $extra = [])
    {
        $this->collect('Date', Carbon::now()->setTimezone('Europe/Moscow')->format('d.m.Y H:i:s'));

        $this->collectHttpData();
        $this->collectRequestData();
        $this->collectUserData();

        if (count($extra) > 0) {
            $this->collect('Extra Data', $extra);
        }

        return $this->data;
    }

    protected function collectHttpData()
    {
        $this->collect('URL', $this->request->fullUrl());

        if (!is_null($route = $this->request->route())) {
            $this->collect('Route', $route->getName());
        }

        $this->collect('Request Method', $this->request->method());
        $this->collect('IP Address', $this->request->ip());
        $this->collect('User Agent', $this->request->header('User-Agent'));
        $this->collect('Referrer', $this->request->header('Referer'));
    }

    protected function collectRequestData()
    {
        $this->collect('Query Data', $_GET);
        $this->collect('Request Data', $_POST);
        $this->collect('Session Data', $this->getSessionData());
        $this->collect('Cookies', $this->request->cookies->all());
    }

    protected function getSessionData()
    {
        try {
            return $this->request->session()->all();
        } catch (\RuntimeException $e) {
            return null;
        }
    }

    protected function collectUserData()
    {
        /**
         * @var \App\Models\User
         */
        $user = $this->request->user();

        if (is_null($user) || !($user instanceof User)) {
            $this->collect('User', 'Not Authenticated');

            return;
        }

        while (\DB::transactionLevel()) {
            \DB::rollBack();
        }

        $this->collect('User ID', $user->id);
        $this->collect('User Key', $user->key);
        $this->collect('User Title', $user->getTitle());
        $this->collect('User Partner ID', intval($user->partner_id));

        if (!is_null($user->email)) {
            $this->collect('User Email', $user->email);
        }

        if (!is_null($user->phone)) {
            $this->collect('User Phone', $user->phone);
        }

        $partner = $user->partner;

        if (is_null($partner) || !($partner instanceof Partner)) {
            return;
        }

        $this->collect('User Partner Title', $partner->title);
        $this->collect('User Partner Key', $partner->key);
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    protected function collect(string $name, $value = null)
    {
        $this->data[$name] = $value ?? '[No Value]';

        return $this;
    }
}
