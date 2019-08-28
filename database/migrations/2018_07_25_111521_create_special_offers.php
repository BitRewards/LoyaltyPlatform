<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpecialOffers extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::beginTransaction();

        Schema::create('special_offers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('image')->nullable();
            $table->string('brand')->nullable();
            $table->integer('action_id');
            $table->integer('weight')->unsigned();
            $table->timestamps();
        });

        DB::commit();
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::beginTransaction();

        Schema::drop('special_offers');

        DB::commit();
    }
}
