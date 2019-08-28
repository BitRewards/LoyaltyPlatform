<?php

use Illuminate\Database\Migrations\Migration;

class AddIndexesOnForeignKeys extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        Artisan::call('db:indexForeignKeys');

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        DB::commit();
    }
}
