<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTokens extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        Schema::create('tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token', 64);
            $table->string('type', 32);
            $table->integer('owner_user_id')->nullable();
            $table->string('destination', 255)->nullable();
            $table->string('destination_type', 10)->nullable();
            $table->jsonb('data')->nullable();
            $table->string('tag', 255)->nullable();

            $table->unique(['id', 'token']);
            $table->index(['token']);

            $table->timestamps();
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        Schema::drop('tokens');

        DB::commit();
    }
}
