<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsersAddReferralPromoCode extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        \Schema::table('users', function (Blueprint $table) {
            $table->string('referral_promo_code')->nullable();
            $table->unique(['partner_id', 'referral_promo_code'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        \Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('referral_promo_code');
        });
    }
}
