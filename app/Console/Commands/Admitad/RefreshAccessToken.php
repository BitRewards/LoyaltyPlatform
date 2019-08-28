<?php

namespace App\Console\Commands\Admitad;

use Admitad\Api\Api;
use App\Services\Settings\AdmitadSettings;
use Illuminate\Console\Command;

class RefreshAccessToken extends Command
{
    /**
     * @var Api
     */
    protected $client;

    /**
     * @var AdmitadSettings
     */
    protected $settings;

    protected $signature = 'admitad:refresh-access-token';

    protected $description = 'Update access token by stored in admitad settings refresh token';

    public function __construct(Api $client, AdmitadSettings $settings)
    {
        parent::__construct();

        $this->client = $client;
        $this->settings = $settings;
    }

    public function handle()
    {
        $requiredFields = ['clientId', 'clientSecret', 'refreshToken'];

        foreach ($requiredFields as $field) {
            if (null === $this->settings->{$field}) {
                throw new \RuntimeException(
                    "Can't refresh token. Necessary field '{$field}' is not defined in settings"
                );
            }
        }

        $response = $this->client->refreshToken(
            $this->settings->clientId,
            $this->settings->clientSecret,
            $this->settings->refreshToken
        );

        $this->settings->refreshToken = $response->getResult('refresh_token');
        $this->settings->update();
    }
}
