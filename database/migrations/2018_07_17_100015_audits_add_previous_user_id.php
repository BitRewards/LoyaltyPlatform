<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AuditsAddPreviousUserId extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        \Schema::table('audits', function (Blueprint $table) {
            $table->integer('previous_user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        \Schema::table('audits', function (Blueprint $table) {
            $table->dropColumn('previous_user_id');
        });
    }
}
