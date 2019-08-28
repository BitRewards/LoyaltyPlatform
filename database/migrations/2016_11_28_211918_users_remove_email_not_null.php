<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsersRemoveEmailNotNull extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \Schema::table('users', function (Blueprint $table) {
            $table->string('email')->change();
        });

        DB::commit();
    }
}
