<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AuthTokensTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::beginTransaction();

        \Schema::create('auth_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('long_ip');
            $table->string('token', 255);
            $table->timestamps();
            $table->timestamp('expired_at')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['token']);
        });

        DB::commit();
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        \Schema::drop('auth_tokens');
    }
}
