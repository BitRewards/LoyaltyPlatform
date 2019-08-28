<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsersAddIsUnsubsribed extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_unsubscribed')->default(false);
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_unsubscribed');
        });

        DB::commit();
    }
}
