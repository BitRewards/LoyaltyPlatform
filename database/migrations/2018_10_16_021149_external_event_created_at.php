<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ExternalEventCreatedAt extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::beginTransaction();

        Schema::table('store_events', function (Blueprint $table) {
            $table->timestamp('external_event_created_at')->nullable();
        });

        Schema::table('store_entities', function (Blueprint $table) {
            $table->integer('last_processed_store_event_id')->nullable()->index();
            $table->foreign('last_processed_store_event_id')
                ->references('id')
                ->on('store_events')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });

        DB::commit();
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::beginTransaction();

        Schema::table('store_events', function (Blueprint $table) {
            $table->dropColumn('external_event_created_at');
        });

        Schema::table('store_entities', function (Blueprint $table) {
            $table->dropColumn('last_processed_store_event_id');
        });

        DB::commit();
    }
}
