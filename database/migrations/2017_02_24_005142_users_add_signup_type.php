<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsersAddSignupType extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('users', function (Blueprint $table) {
            $table->string('signup_type')->default('organic');
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('signup_type');
        });

        DB::commit();
    }
}
