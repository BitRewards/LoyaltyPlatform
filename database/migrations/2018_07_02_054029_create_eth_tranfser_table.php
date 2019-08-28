<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEthTranfserTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::beginTransaction();

        Schema::create('eth_transfers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('from_address')->index();
            $table->string('to_address')->index();
            $table->string('tx_hash')->unique();
            $table->unsignedInteger('receiver_user_id')->nullable()->index();
            $table->string('status')->default('pending')->index();
            $table->jsonb('data')->nullable();
            $table->dateTime('processed_at')->index()->nullable();
            $table->dateTime('created_at')->index();
            $table->dateTime('updated_at')->index()->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('eth_sender_address')->nullable()->unique();
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
            $table->dropColumn('eth_sender_address');
        });

        Schema::drop('eth_transfers');

        DB::commit();
    }
}
