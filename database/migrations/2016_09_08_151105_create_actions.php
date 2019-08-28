<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActions extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        \Schema::create('actions', function (Blueprint $table) {
            $table->increments('id');

            $table->string('type', 32);
            $table->decimal('value', 12, 2);
            $table->string('value_type', 10);

            $table->string('title', 100)->nullable();
            $table->string('description', 255)->nullable();

            $table->decimal('source_order_min_amount', 12, 2)->nullable();

            $table->integer('partner_id');
            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');

            $table->integer('limit_per_user');
            $table->integer('limit_per_user_period');

            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        \Schema::drop('actions');
    }
}
