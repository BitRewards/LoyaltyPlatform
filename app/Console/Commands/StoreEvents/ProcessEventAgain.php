<?php

namespace App\Console\Commands\StoreEvents;

use App\Models\StoreEvent;
use App\Services\StoreEventService;
use Illuminate\Console\Command;

class ProcessEventAgain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storeEvents:ProcessEventAgain {store_event_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Forced process given store_event again ';

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
        $id = $this->argument('store_event_id');
        $event = StoreEvent::whereId($id)->first();

        if (!$event) {
            $this->error("Event with id $id not found");

            exit(1);
        }

        $event->processed_at = null; // kind of dirty hack
        $event->save();

        app(StoreEventService::class)->handleEvent($event);

        $this->info("Successfully processed store_events with id = {$event->id}");
    }
}
