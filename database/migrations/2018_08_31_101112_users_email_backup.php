<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsersEmailBackup extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::beginTransaction();

        Schema::table('users', function (Blueprint $table) {
            $table->string('email_normalized', 255)->nullable()->index();
            $table->string('email_backup', 255)->nullable()->index();
        });

        DB::table('users')->update(['email_backup' => DB::raw('email')]);

        DB::commit();
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::beginTransaction();

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('email_normalized');
            $table->dropColumn('email_backup');
        });

        DB::commit();
    }
}
