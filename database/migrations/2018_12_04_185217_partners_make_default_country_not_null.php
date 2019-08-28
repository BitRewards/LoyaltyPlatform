<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PartnersMakeDefaultCountryNotNull extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::update("UPDATE partners SET default_country = 'ru' WHERE default_country IS NULL");

        \Schema::table('partners', function (Blueprint $table) {
            $table->text('default_country')->notNull()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
    }
}
