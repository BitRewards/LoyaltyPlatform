<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CodesAddIdx extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('codes', function (Blueprint $table) {
            $table->index(['partner_id', 'token']);
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \Schema::table('codes', function (Blueprint $table) {
            $table->dropIndex(['partner_id', 'token']);
        });

        DB::commit();
    }
}
