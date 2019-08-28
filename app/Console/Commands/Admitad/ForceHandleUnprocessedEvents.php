<?php

namespace App\Console\Commands\Admitad;

use App\Models\StoreEvent;
use App\Services\StoreEventService;
use Illuminate\Console\Command;

class ForceHandleUnprocessedEvents extends Command
{
    protected $signature = 'admitad:force-handle-unprocessed-events';

    protected $description = 'Force handle admitad events that do not have a transaction';

    /**
     * @var StoreEventService
     */
    protected $storeEventService;

    public function __construct(StoreEventService $storeEventService)
    {
        parent::__construct();

        $this->storeEventService = $storeEventService;
    }

    public function handle()
    {
        $events = $this->storeEventService->getUnprocessedAffiliateActionEvents();

        $events->each(function (StoreEvent $storeEvent) {
            \DB::beginTransaction();
            $storeEvent->processed_at = null;
            $storeEvent->save();

            $this->storeEventService->handleEvent($storeEvent);
            \DB::commit();
        });
    }
}
