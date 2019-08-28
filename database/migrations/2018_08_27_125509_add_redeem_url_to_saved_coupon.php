<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRedeemUrlToSavedCoupon extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        \Schema::table('saved_coupons', function (Blueprint $table) {
            $table
                ->text('redeem_url')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        \Schema::table('saved_coupons', function (Blueprint $table) {
            $table->dropColumn('redeem_url');
        });
    }
}
