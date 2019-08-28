<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTransactionsBalanceBeforeAfterColumns extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('balance_before', 12, 2)->after('balance_change')->default(0.0)->index();
            $table->decimal('balance_after', 12, 2)->after('balance_before')->default(0.0)->index();
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('balance_before');
            $table->dropColumn('balance_after');
        });

        DB::commit();
    }
}
