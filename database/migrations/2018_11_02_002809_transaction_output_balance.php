<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TransactionOutputBalance extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('output_balance', 30, 18)->nullable();
            $table->timestamp('output_balance_expires_at')->nullable();
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('output_balance');
            $table->dropColumn('output_balance_expires_at');
        });

        DB::commit();
    }
}
