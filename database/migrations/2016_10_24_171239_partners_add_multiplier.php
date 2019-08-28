<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PartnersAddMultiplier extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('partners', function (Blueprint $table) {
            $table->float('money_to_points_multiplier')->default(1);
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn('money_to_points_multiplier');
        });

        DB::commit();
    }
}
