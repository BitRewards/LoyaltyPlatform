<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRewards extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        \Schema::create('rewards', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 32);
            $table->decimal('price', 12, 2);
            $table->decimal('value', 12, 2)->nullable();

            $table->string('title', 100)->nullable();
            $table->string('description', 255)->nullable();

            $table->integer('partner_id');

            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');

            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        \Schema::drop('rewards');
    }
}
