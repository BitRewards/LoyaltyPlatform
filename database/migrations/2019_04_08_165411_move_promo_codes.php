<?php

use App\Models\StoreEntity;
use Illuminate\Database\Migrations\Migration;

class MovePromoCodes extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        \DB::transaction(function () {
            StoreEntity::query()
                ->whereRaw("data->>'promoCode' NOTNULL")
                ->each(function (StoreEntity $storeEntity) {
                    $data = $storeEntity->data->toArray();

                    if (!empty($data['promoCode'])) {
                        $data['promoCodes'] = [$data['promoCode']];
                    }

                    unset($data['promoCode']);

                    $storeEntity->data = $data;
                    $storeEntity->saveOrFail();
                });
        });
    }

    public function down()
    {
    }
}
