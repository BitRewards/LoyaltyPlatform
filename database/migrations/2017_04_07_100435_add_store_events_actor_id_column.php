<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStoreEventsActorIdColumn extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('store_events', function (Blueprint $table) {
            $table->unsignedInteger('actor_id')->after('store_entity_id')->default(0)->index();
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \Schema::table('store_events', function (Blueprint $table) {
            $table->dropColumn('actor_id');
        });

        DB::commit();
    }
}
