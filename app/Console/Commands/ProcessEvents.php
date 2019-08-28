<?php

namespace App\Console\Commands;

use App\Models\StoreEvent;
use App\Services\StoreEventService;
use Illuminate\Console\Command;

class ProcessEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storeEvents:processUnprocessed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processes unprocessed StoreEvents';

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
        $total = StoreEvent::whereNull('processed_at')->count();
        $current = 0;

        StoreEvent::whereNull('processed_at')->orderBy('id', 'desc')->chunk(100, function ($events) use ($total, &$current) {
            foreach ($events as $event) {
                ++$current;
                $this->info("Processing event $current / $total, event id = {$event->id}...");
                app(StoreEventService::class)->handleEvent($event);
            }
        });
    }
}
