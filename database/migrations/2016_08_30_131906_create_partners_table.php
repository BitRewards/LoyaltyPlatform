<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        \Schema::create('partners', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 255);
            $table->string('email', 255);
            $table->integer('giftd_id')->nullable();

            $table->jsonb('customizations')->nullable();

            $table->timestampsTz();
        });

        \Schema::table('users', function (Blueprint $table) {
            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        \Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['partner_id']);
        });

        \Schema::drop('partners');
    }
}
