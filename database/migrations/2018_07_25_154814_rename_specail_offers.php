<?php

use Illuminate\Database\Migrations\Migration;

class RenameSpecailOffers extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::rename('special_offers', 'special_offer_actions');

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \Schema::rename('special_offer_actions', 'special_offers');

        DB::commit();
    }
}
