<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SentSmsPhoneLonger extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('sent_sms', function (Blueprint $table) {
            $table->text('phone')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('sent_sms', function (Blueprint $table) {
            $table->string('phone', 32)->nullable()->change();
        });
    }
}
