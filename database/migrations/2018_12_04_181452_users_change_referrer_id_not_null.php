<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsersChangeReferrerIdNotNull extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        \Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('referrer_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        \Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('referrer_id')->default(0)->change();
        });
    }
}
