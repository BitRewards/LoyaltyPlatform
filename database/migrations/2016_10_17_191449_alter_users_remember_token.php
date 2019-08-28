<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersRememberToken extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('users', function (Blueprint $table) {
            $table->string('remember_token', 60)->nullable()->change();
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        DB::update("UPDATE users SET remember_token = ''");
        \Schema::table('users', function (Blueprint $table) {
            $table->string('remember_token', 30)->nullable()->change();
        });

        DB::commit();
    }
}
