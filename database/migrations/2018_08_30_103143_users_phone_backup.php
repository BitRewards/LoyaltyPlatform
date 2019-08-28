<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsersPhoneBackup extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::beginTransaction();

        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_normalized', 32)->nullable()->index();
            $table->string('phone_backup', 32)->nullable()->index();
        });

        DB::table('users')->update(['phone_backup' => DB::raw('phone')]);

        DB::commit();
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::beginTransaction();

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone_normalized');
            $table->dropColumn('phone_backup');
        });

        DB::commit();
    }
}
