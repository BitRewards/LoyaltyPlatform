<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsersAddPhoneConfirmedAt extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('users', function (Blueprint $table) {
            $table->timestamp('phone_confirmed_at')->nullable();
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone_confirmed_at');
        });

        DB::commit();
    }
}
