<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ActionsAlterDescription extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('actions', function (Blueprint $table) {
            $table->text('description')->change();
            $table->text('title')->change();
        });

        Schema::table('rewards', function (Blueprint $table) {
            $table->text('description')->change();
            $table->text('description_short')->change();
            $table->text('title')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
    }
}
