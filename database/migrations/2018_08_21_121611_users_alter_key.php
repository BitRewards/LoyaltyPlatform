<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsersAlterKey extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::beginTransaction();

        Schema::table('users', function (Blueprint $table) {
            $table->text('key')->change();
            $table->index('key');
        });

        DB::commit();
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::beginTransaction();

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['key']);
        });

        DB::commit();
    }
}
