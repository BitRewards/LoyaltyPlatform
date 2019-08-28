<?php

namespace App\Console\Commands;

use App\Services\StoreEntityService;
use Illuminate\Console\Command;

class AutoFinishStoreEntitiesStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storeEntities:autoFinishStatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Takes StoreEntities which should be finished right now and changes their status either to rejected or confirmed';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        app(StoreEntityService::class)->processAutoFinishingEntities();
    }
}
