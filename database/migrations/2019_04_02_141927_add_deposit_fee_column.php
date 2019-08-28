<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDepositFeeColumn extends Migration
{
    public function up(): void
    {
        Schema::table('partner_deposits', function (Blueprint $table) {
            $table->renameColumn('deposit_amount', 'amount');
            $table->decimal('fee', 12, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('partner_deposits', function (Blueprint $table) {
            $table->renameColumn('amount', 'deposit_amount');
            $table->dropColumn('fee');
        });
    }
}
