<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PartnersAddMainUserId extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('partners', function (Blueprint $table) {
            $table->integer('main_user_id')->nullable();

            $table->foreign('main_user_id')->references('id')->on('users')->onDelete('cascade');
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn('main_user_id');
        });

        DB::commit();
    }
}
