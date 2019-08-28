<?php

namespace App\Console\Commands\Partners;

use App\Models\Action;
use App\Models\Partner;
use App\Services\PartnerService;
use Illuminate\Console\Command;

class RestoreDefaultCustomBonusAction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'partners:restore-custom-bonuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restores deleted custom bonuses (system only).';

    /**
     * Execute the console command.
     *
     * @param PartnerService $partnerService
     *
     * @return mixed
     */
    public function handle(PartnerService $partnerService)
    {
        $partnerIds = \DB::select(
            'select id from partners where id not in '.
            '(select partner_id from actions where type = \''.Action::TYPE_CUSTOM_BONUS.'\' and is_system = true)'
        );

        if (!count($partnerIds)) {
            $this->info('No partners with deleted custom bonuses were found.');

            return;
        }

        $partners = Partner::whereIn('id', collect($partnerIds)->pluck('id')->toArray())->get();

        $partners->each(function (Partner $partner) use ($partnerService) {
            $partnerService->createSystemCustomBonusAction($partner);
        });

        $this->info('Custom bonuses were restored for '.count($partners).' partner(s).');
    }
}
