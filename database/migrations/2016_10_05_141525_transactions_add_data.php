<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TransactionsAddData extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('transactions', function (Blueprint $table) {
            $table->jsonb('data')->nullable();
        });

        \Schema::table('rewards', function (Blueprint $table) {
            $table->jsonb('config')->nullable();
            $table->string('value_type');
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
            $table->dropColumn('data');
        });

        \Schema::table('rewards', function (Blueprint $table) {
            $table->dropColumn('config');
            $table->dropColumn('value_type');
        });

        DB::commit();
    }
}
