<?php

namespace App\Console\Commands\StoreEntities;

use App\Models\StoreEntity;
use App\Models\StoreEvent;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class RemoveDuplicateEntities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store-entities:remove-duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove invalid duplicate transactions along with store_events and store_entities';

    /**
     * @param Collection $entities
     * @param bool       $realDelete
     *
     * @return int
     *
     * @throws \Exception
     */
    private function deleteEntities(Collection $entities, $realDelete = false)
    {
        if (empty($entities)) {
            return 0;
        }

        /* @var StoreEntity $entity */
        foreach ($entities as $entity) {
            if ($realDelete) {
                StoreEvent::with('entity')
                          ->where('store_entity_id', $entity->id)
                          ->delete();
                $entity->delete();
            } else {
                echo "Going to delete store_entity #{$entity->id}:\n";
                echo \HJson::encode($entity->getAttributes())."\n";

                $events = StoreEvent::where('store_entity_id', $entity->id)->get();
                echo "Events:\n";

                foreach ($events as $event) {
                    echo \HJson::encode($event->getAttributes())."\n";
                }

                $transactions = Transaction::where('source_store_entity_id', $entity->id)->get();
                echo "Transactions:\n";

                foreach ($transactions as $transaction) {
                    echo \HJson::encode($transaction->getAttributes())."\n";
                }
            }
        }

        return count($entities);
    }

    private function findOlderEntities(Collection $entities)
    {
        if (count($entities) > 1) {
            $entities->shift();

            return $entities;
        }

        return [];
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->confirm('This command will delete some data. Did you make a backup?')) {
            $this->info('Exiting. No models were touched.');

            return;
        }

        $realDelete = $this->confirm('Delete really? Answer no to run simulation');

        $deletedCnt = 0;

        StoreEntity::with('partner')
            ->select(\DB::raw('partner_id, external_id, 
                SUM(case when status = \'pending\' then 1 else 0 end) as pending, 
                SUM(case when status = \'confirmed\' then 1 else 0 end) as confirmed, 
                SUM(case when status = \'rejected\' then 1 else 0 end) as rejected, 
                count(*) as cnt'))
            ->groupBy('partner_id', 'external_id')
            ->having(\DB::raw('count(*)'), '>', 1)
            ->orderBy('partner_id', 'asc')
            ->orderBy('external_id', 'asc')
            ->chunk(500, function ($rows) use (&$deletedCnt, $realDelete) {
                foreach ($rows as $grouped) {
                    $entities = StoreEntity::with('partner')
                        ->where('partner_id', '=', $grouped->partner_id)
                        ->where('external_id', '=', $grouped->external_id)
                        ->where('status', '=', StoreEntity::STATUS_PENDING)
                        ->orderBy('updated_at', 'desc')
                        ->orderBy('id', 'desc')
                        ->get();

                    if ($grouped->confirmed > 0 || $grouped->rejected > 0) {
                        // delete all pending
                        $deletedCnt += $this->deleteEntities($entities, $realDelete);
                    } else {
                        $deletedCnt += $this->deleteEntities($this->findOlderEntities($entities), $realDelete);
                    }
                }
            });

        if ($realDelete) {
            $this->info($deletedCnt.' duplicate entities deleted.');
        } else {
            $this->info($deletedCnt.' duplicate entities would be deleted if you run this command in live mode.');
        }
    }
}
