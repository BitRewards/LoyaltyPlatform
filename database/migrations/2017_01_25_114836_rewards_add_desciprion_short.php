<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RewardsAddDesciprionShort extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('rewards', function (Blueprint $table) {
            $table->string('description_short')->nullable();
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \Schema::table('rewards', function (Blueprint $table) {
            $table->dropColumn('description_short');
        });

        DB::commit();
    }
}
