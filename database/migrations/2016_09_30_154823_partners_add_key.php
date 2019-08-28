<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PartnersAddKey extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('partners', function (Blueprint $table) {
            $table->string('key', '32');

            $table->unique(['key']);
        });

        DB::commit();
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::beginTransaction();

        \Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn('key');
        });

        DB::commit();
    }
}
