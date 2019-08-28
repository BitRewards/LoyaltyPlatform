<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StoreEventsAddConverterType extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('store_events', function (Blueprint $table) {
            $table->string('converter_type', 32)->nullable();
            $table->jsonb('raw_data')->nullable();
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \Schema::table('store_events', function (Blueprint $table) {
            $table->dropColumn('converter_type');
            $table->dropColumn('raw_data');
        });

        DB::commit();
    }
}
