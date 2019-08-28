<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPartnerBalance extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('partners', function (Blueprint $table) {
            $table->decimal('balance', 30, 18)->default(0);
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn('balance');
        });

        DB::commit();
    }
}
