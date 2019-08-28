<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CodesAddAcquiredAt extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('codes', function (Blueprint $table) {
            $table->timestamp('acquired_at')->nullable();
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \Schema::table('codes', function (Blueprint $table) {
            $table->dropColumn('acquired_at');
        });

        DB::commit();
    }
}
