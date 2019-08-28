<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersPicture extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('users', function (Blueprint $table) {
            $table->string('picture')->nullable()->change();
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        DB::update("UPDATE users SET picture = ''");
        \Schema::table('users', function (Blueprint $table) {
            $table->string('picture', 100)->nullable()->change();
        });

        DB::commit();
    }
}
