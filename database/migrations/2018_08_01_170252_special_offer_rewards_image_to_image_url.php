<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SpecialOfferRewardsImageToImageUrl extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::beginTransaction();

        Schema::table('special_offer_rewards', function (Blueprint $table) {
            $table->renameColumn('image', 'image_url');
        });

        Schema::table('special_offer_actions', function (Blueprint $table) {
            $table->renameColumn('image', 'image_url');
        });

        DB::commit();
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::beginTransaction();

        Schema::table('special_offer_rewards', function (Blueprint $table) {
            $table->renameColumn('image_url', 'image');
        });

        Schema::table('special_offer_actions', function (Blueprint $table) {
            $table->renameColumn('image_url', 'image');
        });

        DB::commit();
    }
}
