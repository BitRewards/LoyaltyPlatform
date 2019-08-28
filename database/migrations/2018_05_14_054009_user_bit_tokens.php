<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserBitTokens extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::beginTransaction();

        Schema::table('users', function (Blueprint $table) {
            $table->string('bit_tokens_sender_address')->nullable()->unique();
        });

        Schema::create('bit_tokens_sender_address_history', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable()->index();
            $table->string('address')->index();
            $table->timestamp('created_at')->index();
        });

        DB::commit();
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::beginTransaction();

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('bit_tokens_sender_address');
        });

        Schema::drop('bit_tokens_sender_address_history');

        DB::commit();
    }
}
