<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ActionsAlterLimitsNull extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('actions', function (Blueprint $table) {
            $table->integer('limit_per_user')->nullable()->change();
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
            $table->dropColumn('limit_per_user');
        });

        DB::commit();
    }
}
