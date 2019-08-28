<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PartnersTreasuryIntegration extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::beginTransaction();

        Schema::table('partners', function (Blueprint $table) {
            $table->string('eth_address')->nullable()->index();
            $table->string('withdraw_key')->nullable()->index();
        });

        DB::commit();
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::beginTransaction();

        Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn('eth_address');
            $table->dropColumn('withdraw_key');
        });

        DB::commit();
    }
}
