<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ActionsDropLimitPerUserPeriod extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('actions', function (Blueprint $table) {
            $table->dropColumn('limit_per_user_period');
            $table->integer('limit_min_time_between')->nullable();
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
            $table->integer('limit_per_user_period')->nullable();
            $table->dropColumn('limit_min_time_between');
        });

        DB::commit();
    }
}
