<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PartnersAddEventbriteOauthKey extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('partners', function (Blueprint $table) {
            $table->string('eventbrite_oauth_token')->nullable();
            $table->string('eventbrite_url')->nullable();
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn('eventbrite_oauth_token');
            $table->dropColumn('eventbrite_url');
        });

        DB::commit();
    }
}
