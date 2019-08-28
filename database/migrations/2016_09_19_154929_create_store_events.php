<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreEvents extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::beginTransaction();

        \Schema::create('store_entities', function (Blueprint $table) {
            $table->increments('id');

            $table->string('type', 64);
            $table->string('external_id', 64)->nullable();
            $table->timestampTz('confirmed_at')->nullable();

            $table->jsonb('data')->nullable();

            $table->integer('partner_id');

            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('restrict');

            $table->index(['type', 'external_id']);

            $table->timestampsTz();
        });

        \Schema::create('store_events', function (Blueprint $table) {
            $table->increments('id');

            $table->string('action', 64);
            $table->string('entity_type', 64)->nullable();
            $table->string('entity_external_id', 64)->nullable();
            $table->jsonb('data')->nullable();

            $table->integer('partner_id');

            $table->integer('store_entity_id')->nullable();

            $table->timestampTz('processed_at')->nullable();

            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('restrict');
            $table->foreign('store_entity_id')->references('id')->on('store_entities')->onDelete('restrict');

            $table->index(['entity_type', 'entity_external_id']);

            $table->timestampsTz();
        });

        \Schema::table('transactions', function (Blueprint $table) {
            $table->integer('source_store_event_id')->nullable();
            $table->integer('source_store_entity_id')->nullable();

            $table->foreign('source_store_event_id')->references('id')->on('store_events')->onDelete('cascade');
            $table->foreign('source_store_entity_id')->references('id')->on('store_entities')->onDelete('cascade');
        });

        DB::commit();
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::beginTransaction();

        \Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('source_store_event_id');
            $table->dropColumn('source_store_entity_id');
        });

        \Schema::drop('store_events');
        \Schema::drop('store_entities');

        DB::commit();
    }
}
