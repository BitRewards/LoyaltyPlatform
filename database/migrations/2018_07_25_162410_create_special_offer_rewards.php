<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpecialOfferRewards extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        Schema::create('special_offer_rewards', function (Blueprint $table) {
            $table->increments('id');
            $table->string('image')->nullable();
            $table->string('brand')->nullable();
            $table->integer('reward_id');
            $table->integer('weight')->unsigned();
            $table->timestamps();
            $table->foreign('reward_id')->references('id')->on('rewards')->onDelete('cascade');
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        Schema::drop('special_offer_rewards');

        DB::commit();
    }
}
