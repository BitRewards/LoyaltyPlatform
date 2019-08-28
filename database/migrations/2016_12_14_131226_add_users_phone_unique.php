<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUsersPhoneUnique extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('users', function (Blueprint $table) {
            $table->unique(['phone']);
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \Schema::table('users', function (Blueprint $table) {
            \DB::statement('DROP INDEX IF EXISTS users_phone_unique;');
        });

        DB::commit();
    }
}
