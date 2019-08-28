<?php

namespace App\Console\Commands\Admitad;

use App\Services\Settings\AdmitadSettings;
use Illuminate\Console\Command;

class UpdateConfig extends Command
{
    /**
     * @var AdmitadSettings
     */
    protected $settings;

    protected $signature = 'admitad:update-config {--clientId=} {--clientSecret=} {--accessToken=} {--refreshToken=} {--partnerKey=}';

    protected $description = 'Set access token for admitad api';

    public function __construct(AdmitadSettings $settings)
    {
        parent::__construct();

        $this->settings = $settings;
    }

    public function handle()
    {
        foreach ($this->settings->getAvailableOptions() as $option) {
            if (null !== $this->option($option)) {
                $this->settings->{$option} = $this->option($option);
            }
        }

        $this->settings->update();
    }
}
