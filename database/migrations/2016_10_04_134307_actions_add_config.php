<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ActionsAddConfig extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('actions', function (Blueprint $table) {
            $table->jsonb('config')->nullable();

            $table->dropColumn('source_order_min_amount');
        });

        DB::commit();
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::beginTransaction();

        \Schema::table('actions', function (Blueprint $table) {
            $table->dropColumn('config');

            $table->decimal('source_order_min_amount', 12, 2)->nullable();
        });

        DB::commit();
    }
}
