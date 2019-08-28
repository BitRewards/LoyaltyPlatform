<?php

namespace App\Console\Commands\Partners;

use App\Models\Partner;
use App\Services\CustomizationsService;
use Illuminate\Console\Command;

class MigrateCustomizations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'partners:migrate-customizations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrates Partner customizations from model to settings.';

    /**
     * Execute the console command.
     *
     * @param CustomizationsService $customizations
     */
    public function handle(CustomizationsService $customizations)
    {
        $partners = Partner::all();

        $partners->each(function (Partner $partner) use ($customizations) {
            $customizations->migrateCustomizations($partner);
        });

        $this->info('Everything done.');
    }
}
