<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TransactionChildRefillIndex extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::beginTransaction();
        DB::statement("CREATE UNIQUE INDEX transactions_data_child_refill_bit_id ON transactions ( (data->'".\App\Models\Transaction::DATA_CHILD_REFILL_BIT_TRANSACTION_ID."') ) ;");
        DB::commit();
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::beginTransaction();

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('transactions_data_child_refill_bit_id');
        });

        DB::commit();
    }
}
