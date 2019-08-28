<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OutputBalanceIndexInTransactions extends Migration
{
    public function up(): void
    {
        DB::beginTransaction();

        \Schema::table('transactions', function (Blueprint $table) {
            $table->index(['type', 'output_balance']);
        });

        DB::commit();
    }

    public function down(): void
    {
        DB::beginTransaction();

        \Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('transactions_type_output_balance_index');
        });

        DB::commit();
    }
}
