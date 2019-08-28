<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RewardsAlterPrice extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        \Schema::table('rewards', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
    }
}
