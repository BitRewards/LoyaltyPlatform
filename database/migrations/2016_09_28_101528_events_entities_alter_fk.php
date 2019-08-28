<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EventsEntitiesAlterFk extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('store_events', function (Blueprint $table) {
            $table->dropForeign(['partner_id']);
            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');
        });

        \Schema::table('store_entities', function (Blueprint $table) {
            $table->dropForeign(['partner_id']);
            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');
        });

        DB::commit();
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::beginTransaction();

        DB::commit();
    }
}
