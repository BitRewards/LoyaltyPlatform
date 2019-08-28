<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RewardPriceTypes extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::beginTransaction();

        Schema::table('rewards', function (Blueprint $table) {
            $table->string('price_type')->string()->default(\App\Models\Reward::PRICE_TYPE_POINTS)->index();
        });

        DB::commit();
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::beginTransaction();

        Schema::table('rewards', function (Blueprint $table) {
            $table->dropColumn('price_type');
        });

        DB::commit();
    }
}
