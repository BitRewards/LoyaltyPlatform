<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FloatBalanceAndPoints extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::beginTransaction();
        \Schema::table('users', function (Blueprint $table) {
            $table->decimal('balance', 30, 18)->change();
        });

        \Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('balance_before', 30, 18)->change();
            $table->decimal('balance_after', 30, 18)->change();
            $table->decimal('balance_change', 30, 18)->change();
        });
        DB::commit();
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::beginTransaction();
        \Schema::table('users', function (Blueprint $table) {
            $table->decimal('balance', 12, 2)->change();
        });

        \Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('balance_before', 12, 2)->change();
            $table->decimal('balance_after', 12, 2)->change();
            $table->decimal('balance_change', 12, 2)->change();
        });
        DB::commit();
    }
}
