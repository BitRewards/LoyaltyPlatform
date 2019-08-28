<?php

namespace App\Jobs;

use App\Models\StoreEvent;
use App\Services\StoreEventService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleStoreEvent implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $eventId;

    public function __construct($eventId)
    {
        $this->eventId = $eventId;
    }

    public function handle()
    {
        try {
            $storeEvent = StoreEvent::find($this->eventId);
        } catch (\Exception $e) {
            \DB::rollBack();
            $storeEvent = StoreEvent::find($this->eventId);
        }

        try {
            app(StoreEventService::class)->handleEvent($storeEvent);
        } catch (\Throwable $e) {
            \DB::rollBack();
            \Log::error('Unable to process StoreEvent: '.$e->getMessage(), [$e, $storeEvent]);
        }
    }
}
