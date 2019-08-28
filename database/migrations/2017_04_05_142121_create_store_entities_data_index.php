<?php

use Illuminate\Database\Migrations\Migration;

class CreateStoreEntitiesDataIndex extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        DB::statement(
            'CREATE INDEX store_entities_data_promo_codes_idx ON store_entities '.
            "USING gin ((data->'promoCodes') jsonb_path_ops);"
        );

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        DB::statement('DROP INDEX IF EXISTS store_entities_data_promo_codes_idx');

        DB::commit();
    }
}
