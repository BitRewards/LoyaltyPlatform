<?php

namespace App\Console\Commands\StoreEntities;

use App\Models\StoreEntity;
use App\Models\StoreEvent;
use App\Services\EventbriteService;
use App\Services\EventDataConverters\EventbriteOrder;
use Illuminate\Console\Command;

class FixEventbritePromocodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eventbrite:fix-promocodes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fills store entities with missed eventbrite promocodes.';

    /**
     * Execute the console command.
     *
     * @param EventbriteService $eventbrite
     */
    public function handle(EventbriteService $eventbrite)
    {
        if (!$this->confirm('This command will update StoreEvent models. Are you sure you want to continue?')) {
            $this->info('OK, no models were touched.');

            return;
        }

        $events = StoreEvent::with('entity', 'partner')
            ->where('action', StoreEvent::ACTION_STORE)
            ->where('entity_type', StoreEntity::TYPE_ORDER)
            ->where('converter_type', StoreEvent::CONVERTER_TYPE_EVENTBRITE_ORDER)
            ->whereNotNull('entity_external_id')
            ->get();

        if (!count($events)) {
            $this->info('No StoreEvents were found.');

            return;
        }

        $this->info('Got '.count($events).' to process.');

        $updatedCount = $events->sum(function (StoreEvent $event) use ($eventbrite) {
            $this->comment('Processing StoreEvent #'.$event->id);

            if (!($event->entity instanceof StoreEntity)) {
                $this->error('Event\'s entity is broken (type: "'.gettype($event->entity).'"');

                return 0;
            }

            try {
                $orderData = $eventbrite->getOrderData($event->partner, $event->entity_external_id);
            } catch (\Exception $e) {
                $this->error('getOrderData exception: "'.$e->getMessage().'"');

                return 0;
            }

            $eventData = $event->data;
            $event->raw_data = (is_string($orderData) ? json_decode($orderData, true) : $orderData);

            $converter = new EventbriteOrder($event);
            $promoCodes = $converter->getPromoCodes();

            if (!count($promoCodes) || $promoCodes === $eventData->promoCodes) {
                $this->info('Promocodes list is empty or identical with current list.');

                return 0;
            }

            $eventData->promoCodes = $promoCodes;

            try {
                \DB::transaction(function () use ($event, $eventData) {
                    $event->data = $eventData;
                    $event->save();

                    $event->entity->data = $event->data;
                    $event->entity->save();
                });
            } catch (\Exception $e) {
                $this->error('Got exception while saving event & entity data: "'.$e->getMessage().'"');

                return 0;
            }

            $this->info('StoreEvent #'.$event->id.' was processed successfully.');

            return 1;
        });

        $this->info($updatedCount.' events & entities were updated.');
    }
}
