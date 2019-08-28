<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSpecialOfferRelations extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('special_offers', function (Blueprint $table) {
            $table->foreign('action_id')->references('id')->on('actions')->onDelete('cascade');
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \Schema::table('special_offers', function (Blueprint $table) {
            $table->dropForeign('special_offers_action_id_foreign');
        });

        DB::commit();
    }
}
