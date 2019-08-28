<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PartnersDefaultCountry extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::beginTransaction();

        Schema::table('partners', function (Blueprint $table) {
            $table->char('default_country', 2)->nullable()->index();
        });

        DB::update("UPDATE partners SET default_country = 'ru'");

        DB::commit();
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::beginTransaction();

        Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn('default_country');
        });

        DB::commit();
    }
}
