<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ActionsAddStatus extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('actions', function (Blueprint $table) {
            $table->string('status', '10');
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
            $table->dropColumn('status');
        });

        DB::commit();
    }
}
