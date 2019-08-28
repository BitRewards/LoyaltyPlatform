<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCodes extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::create('codes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token');

            $table->decimal('bonus_points', 12, 2)->nullable();

            $table->integer('user_id')->nullable();
            $table->integer('partner_id');

            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unique(['partner_id', 'token']);

            $table->timestamps();
        });

        Artisan::call('db:indexForeignKeys');

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \Schema::drop('codes');

        DB::commit();
    }
}
