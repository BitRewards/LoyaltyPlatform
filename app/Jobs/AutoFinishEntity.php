<?php

namespace App\Jobs;

use App\Models\StoreEntity;
use App\Services\StoreEntityService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AutoFinishEntity implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $entityId;

    public function __construct($entityId)
    {
        $this->entityId = $entityId;
    }

    public function handle()
    {
        $entity = StoreEntity::whereId($this->entityId)->first();

        if (!$entity) {
            sleep(3);
            $entity = StoreEntity::whereId($this->entityId)->first();

            if (!$entity) {
                return;
            }
        }

        app(StoreEntityService::class)->autoFinishEntity($entity);
    }
}
