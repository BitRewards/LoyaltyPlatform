<?php

namespace App\Console\Commands\Admitad;

use App\Services\AdmitadService;
use App\Services\Settings\AdmitadSettings;
use Illuminate\Console\Command;

class SyncEvents extends Command
{
    protected $signature = 'admitad:sync-events {--skipDuplicates=1}';

    protected $description = 'Sync admitad events';

    /**
     * @var AdmitadService
     */
    protected $admitadService;

    /**
     * @var AdmitadSettings
     */
    protected $admitadSettings;

    public function __construct(AdmitadService $admitadService, AdmitadSettings $admitadSettings)
    {
        parent::__construct();

        $this->admitadService = $admitadService;
        $this->admitadSettings = $admitadSettings;
    }

    public function handle()
    {
        $partner = $this->admitadSettings->getPartner();

        $skipDuplicates = (bool) $this->option('skipDuplicates');

        if (!$partner) {
            throw new \RuntimeException('Partner key not defined in admitad settings');
        }

        $this->admitadService->sync($partner, new \DateInterval('P3M'), $skipDuplicates);
    }
}
