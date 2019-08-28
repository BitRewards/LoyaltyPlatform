<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionOutputs extends Migration
{
    public function up(): void
    {
        DB::beginTransaction();

        \Schema::create('transaction_outputs', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('transaction_from_id');
            $table->integer('transaction_to_id');
            $table->decimal('amount', 30, 18);
            $table->timestamps();

            $table->foreign('transaction_from_id')->references('id')->on('transactions')->onDelete('cascade');
            $table->foreign('transaction_to_id')->references('id')->on('transactions')->onDelete('cascade');
        });

        DB::commit();
    }

    public function down(): void
    {
        DB::beginTransaction();

        \Schema::drop('transaction_outputs');

        DB::commit();
    }
}
