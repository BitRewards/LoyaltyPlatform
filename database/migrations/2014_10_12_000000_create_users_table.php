<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        \Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key', 32);
            $table->string('email', 255);

            $table->timestampTz('email_confirmed_at')->nullable();

            $table->string('name', 100)->nullable();
            $table->string('picture', 100)->nullable();
            $table->decimal('balance', 12, 2)->default(0);
            $table->integer('partner_id')->nullable();
            $table->string('password', 64)->nullable();
            $table->string('vk_id', 32)->nullable();
            $table->string('fb_id', 64)->nullable();
            $table->string('vk_token', 255)->nullable();
            $table->string('fb_token', 255)->nullable();

            $table->string('remember_token', 32)->nullable();

            $table->string('role', 15)->nullable();
            $table->string('api_key', 32)->nullable();
            $table->integer('giftd_id')->nullable();

            $table->unique(['email', 'partner_id']);
            $table->index(['partner_id']);

            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('users');
    }
}
