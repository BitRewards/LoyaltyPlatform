<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ActionsAddIsDisplayedToUsers extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('actions', function (Blueprint $table) {
            $table->boolean('is_displayed_to_users')->default(true);
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \Schema::table('actions', function (Blueprint $table) {
            $table->dropColumn('is_displayed_to_users');
        });

        DB::commit();
    }
}
