<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactions extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        \Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('balance_change', 12, 2);

            $table->string('comment', 255)->nullable();

            $table->string('status', 16);
            $table->timestampTz('confirmed_at');

            $table->integer('action_id')->nullable();
            $table->integer('reward_id')->nullable();
            $table->integer('source_order_id')->nullable();

            $table->integer('user_id');
            $table->integer('partner_id');

            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->foreign('action_id')->references('id')->on('actions')->onDelete('cascade');
            $table->foreign('reward_id')->references('id')->on('rewards')->onDelete('cascade');
            $table->foreign('source_order_id')->references('id')->on('orders')->onDelete('cascade');

            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        \Schema::drop('transactions');
    }
}
