<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConfirmedAtToPartnerDeposits extends Migration
{
    public function up(): void
    {
        DB::beginTransaction();

        Schema::table('partner_deposits', function (Blueprint $table) {
            $table->smallInteger('currency')->default(HAmount::CURRENCY_RUB);
            $table->timestamp('confirmed_at')->nullable();
        });

        DB::commit();
    }

    public function down(): void
    {
        DB::beginTransaction();

        Schema::table('partner_deposits', function (Blueprint $table) {
            $table->dropColumn('confirmed_at');
            $table->dropColumn('currency');
        });

        DB::commit();
    }
}
