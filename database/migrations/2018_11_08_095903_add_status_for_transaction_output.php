<?php

use App\Models\TransactionOutput;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusForTransactionOutput extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('transaction_outputs', function (Blueprint $table) {
            $table->string('status')->nullable();
        });

        DB::update('UPDATE transaction_outputs SET status = ?', [TransactionOutput::STATUS_CONFIRMED]);

        \Schema::table('transaction_outputs', function (Blueprint $table) {
            $table->string('status')->nullable(false)->change();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \Schema::table('transaction_outputs', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('confirmed_at');
            $table->dropColumn('rejected_at');
        });

        DB::commit();
    }
}
