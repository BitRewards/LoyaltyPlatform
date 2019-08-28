<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ActionsAndRewardsAddTagIndex extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('actions', function (Blueprint $table) {
            $table->index(['partner_id', 'tag', 'status', 'is_displayed_to_users']);
        });

        \Schema::table('rewards', function (Blueprint $table) {
            $table->index(['partner_id', 'tag', 'status']);
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        DB::commit();
    }
}
