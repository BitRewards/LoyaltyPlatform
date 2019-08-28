<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StoreEntitiesAddStatusAutoFinishesAt extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('store_entities', function (Blueprint $table) {
            $table->timestamp('status_auto_finishes_at')->nullable();
            $table->string('status', 16);

            $table->index(['status_auto_finishes_at', 'status']);
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \Schema::table('store_entities', function (Blueprint $table) {
            $table->dropColumn('status_auto_finishes_at');
            $table->dropColumn('status');
        });

        DB::commit();
    }
}
