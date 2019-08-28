<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSentSms extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        Schema::create('sent_sms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('phone', 16);
            $table->string('text', 255);
            $table->timestamps();
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        Schema::drop('sent_sms');

        DB::commit();
    }
}
