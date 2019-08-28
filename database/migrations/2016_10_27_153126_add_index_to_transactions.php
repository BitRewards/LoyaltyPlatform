<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToTransactions extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('transactions', function (Blueprint $table) {
            $table->index(['user_id', 'created_at']);
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at']);
        });

        DB::commit();
    }
}
