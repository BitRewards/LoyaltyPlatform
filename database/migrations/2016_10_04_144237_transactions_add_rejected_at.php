<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TransactionsAddRejectedAt extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('transactions', function (Blueprint $table) {
            $table->timestampTz('rejected_at')->nullable();
            $table->dropColumn('confirmed_at');
        });

        \Schema::table('transactions', function (Blueprint $table) {
            $table->timestampTz('confirmed_at')->nullable();
        });

        \Schema::table('store_entities', function (Blueprint $table) {
            $table->timestampTz('rejected_at')->nullable();
        });

        DB::commit();
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::beginTransaction();

        \Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('rejected_at');
        });

        \Schema::table('store_entities', function (Blueprint $table) {
            $table->dropColumn('rejected_at');
        });

        DB::commit();
    }
}
