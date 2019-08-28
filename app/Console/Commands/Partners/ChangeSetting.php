<?php

namespace App\Console\Commands\Partners;

use App\Models\Partner;
use App\Services\PartnerService;
use Illuminate\Console\Command;

class ChangeSetting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'partner:change-setting {partner_key} {settingName} {settingValue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Changes one setting for a partner';

    /**
     * Execute the console command.
     *
     * @param PartnerService $partnerService
     *
     * @return mixed
     */
    public function handle()
    {
        $partnerKey = $this->argument('partner_key');
        $setting = $this->argument('settingName');
        $value = $this->argument('settingValue');

        if (!strlen($value)) {
            $value = null;
        }

        $partner = Partner::where('key', $partnerKey)->first();

        if (!$partner) {
            $this->error("Partner with key '$partnerKey' not found");

            exit(1);
        }

        $valueStr = json_encode($value);

        $partner->setSetting($setting, $value);

        $partner->save();

        $this->info("Successfully set  '$setting' for partner {$partner->title} to $valueStr");
    }
}
