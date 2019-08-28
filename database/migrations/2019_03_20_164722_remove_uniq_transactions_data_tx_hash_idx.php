<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUniqTransactionsDataTxHashIdx extends Migration
{
    /**
     * Reverse the migrations.
     */
    public function up()
    {
        DB::beginTransaction();

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('transactions_data_tx_hash_idx');
            $table->dropIndex('transactions_data_magic_number_idx');
        });

        DB::commit();
    }

    /**
     * Run the migrations.
     */
    public function down()
    {
        DB::beginTransaction();
        DB::statement("CREATE UNIQUE INDEX transactions_data_tx_hash_idx ON transactions ( (data->'treasury_tx_hash') ) ;");
        DB::statement("CREATE UNIQUE INDEX transactions_data_magic_number_idx ON transactions ( (data->'magicNumber') ) ;");
        DB::commit();
    }
}
