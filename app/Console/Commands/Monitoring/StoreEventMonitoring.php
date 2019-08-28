<?php

namespace App\Console\Commands\Monitoring;

use App\Models\StoreEvent;
use Illuminate\Console\Command;
use Illuminate\Mail\Message;

class StoreEventMonitoring extends Command
{
    protected $signature = 'monitoring:store-event {--min-count=30 : minimum unprocessed events for triggering alert }';

    protected $description = 'Monitoring count of unprocessed store events';

    public function handle()
    {
        $count = StoreEvent::whereNull('processed_at')->count();

        if ($count >= $this->option('min-count')) {
            \Mail::raw(
                '',
                function (Message $message) use ($count) {
                    $message->subject("В лояльности {$count} необработаннных StoreEvent!");
                    $message->to('**REMOVED**');
                }
            );
        }
    }
}
