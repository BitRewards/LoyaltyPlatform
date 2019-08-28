<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTagToRewardsAndActions extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \Schema::table('rewards', function (Blueprint $table) {
            $table->string('tag')->default('');
            $table->index(['partner_id', 'tag']);
        });

        \Schema::table('actions', function (Blueprint $table) {
            $table->string('tag')->default('');
            $table->index(['partner_id', 'tag']);
        });

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \Schema::table('rewards', function (Blueprint $table) {
            $table->dropColumn('tag');
        });

        \Schema::table('actions', function (Blueprint $table) {
            $table->dropColumn('tag');
        });

        DB::commit();
    }
}
