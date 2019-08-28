<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrders extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        \Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('external_id', 64);
            $table->decimal('amount_total', 12, 2);
            $table->string('email', 255);

            $table->string('promo_code', 100);

            $table->integer('referred_by_user_id')->nullable();

            $table->integer('user_id')->nullable();

            $table->string('status', 16);
            $table->timestampTz('confirmed_at');

            $table->integer('partner_id');

            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('referred_by_user_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        \Schema::drop('orders');
    }
}
