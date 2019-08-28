<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SentEmails extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::create('sent_emails', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('email', 255);
            $table->string('subject', 512);
            $table->longText('body');
            $table->string('token', 32);
            $table->index(['email']);
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \Schema::drop('sent_emails');

        DB::commit();
    }
}
