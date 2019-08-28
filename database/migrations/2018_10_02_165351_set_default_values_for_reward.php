<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetDefaultValuesForReward extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('rewards', function (Blueprint $table) {
            $table->text('value_type')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('rewards', function (Blueprint $table) {
            $table->text('value_type')->nullable(false)->change();
        });
    }
}
