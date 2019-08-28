<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PartnersAddDefaultLanguage extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('partners', function (Blueprint $table) {
            $table->string('default_language', '8')->default('ru');
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn('default_language');
        });

        DB::commit();
    }
}
