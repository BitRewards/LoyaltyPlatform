<?php

use Illuminate\Database\Migrations\Migration;

class TransactionAddDataIdx extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \DB::statement("CREATE INDEX transactions__promo_code__idx ON transactions((data->>'promo_code'));");

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \DB::statement('DROP INDEX transactions__promo_code__idx');

        DB::commit();
    }
}
