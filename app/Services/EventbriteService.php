<?php

namespace App\Services;

use App\Models\Partner;
use jamiehollern\eventbrite\Eventbrite;

class EventbriteService
{
    public function getOauthUrl()
    {
        return 'https://www.eventbrite.com/oauth/authorize?response_type=code&client_id='.config('services.eventbrite.client_key');
    }

    public function handleOauthRedirect(Partner $partner, $code)
    {
        $oauthResponse = \HHttp::doPost('https://www.eventbrite.com/oauth/token', [
            'code' => $code,
            'client_secret' => config('services.eventbrite.client_secret'),
            'client_id' => config('services.eventbrite.client_key'),
            'grant_type' => 'authorization_code',
        ]);

        $oauthToken = \HJson::decode($oauthResponse)['access_token'];

        $this->doInstall($partner, $oauthToken);
    }

    private function doInstall(Partner $partner, $oauthToken)
    {
        $partner->eventbrite_oauth_token = $oauthToken;
        $partner->save();

        $client = new Eventbrite($partner->eventbrite_oauth_token);

        $myOrganizers = $client->call('GET', 'users/me/organizers/', []);

        if ($myOrganizers['body']['organizers'] ?? []) {
            $partner->eventbrite_url = $myOrganizers['body']['organizers'][0]['url'];
            $partner->save();
        }

        $result = $client->call('POST', 'webhooks/', ['form_params' => [
            'endpoint_url' => route('eventbrite.orderWebhook', ['api_token' => $partner->mainAdministrator->api_token]),
            'actions' => 'order.updated,order.refunded,order.placed,attendee.checked_in,attendee.checked_out,attendee.updated,event.created,event.published,event.updated,event.unpublished',
        ]]);

        $this->updatePartnersConfigToEventbriteCompatible($partner);

        return $result;
    }

    private function updatePartnersConfigToEventbriteCompatible(Partner $partner)
    {
        if (!$partner->getSetting(Partner::SETTINGS_EVENTBRITE_AUTO_CONFIRM_TRANSACTIONS_AFTER_EVENT_START_INTERVAL)) {
            $partner->setSetting(Partner::SETTINGS_EVENTBRITE_AUTO_CONFIRM_TRANSACTIONS_AFTER_EVENT_START_INTERVAL, -3 * 24 * 3600);
        }

        if (!$partner->getSetting(Partner::SETTINGS_AUTO_SIGNUP_USERS_FROM_ORDERS)) {
            $partner->setSetting(Partner::SETTINGS_AUTO_SIGNUP_USERS_FROM_ORDERS, true);
        }

        $partner->save();
    }

    public function getOrderData(Partner $partner, $orderId)
    {
        $client = new Eventbrite($partner->eventbrite_oauth_token);

        $response = $client->call('GET', "orders/$orderId", [
            'query' => [
                'expand' => 'event,attendees.promotional_code',
            ],
        ]);

        return $response['body'] ?? null;
    }
}
