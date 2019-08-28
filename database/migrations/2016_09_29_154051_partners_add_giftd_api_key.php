<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PartnersAddGiftdApiKey extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('partners', function (Blueprint $table) {
            $table->string('giftd_api_key', '32')->nullable();
        });

        DB::commit();
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::beginTransaction();

        \Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn('giftd_api_key');
        });

        DB::commit();
    }
}
