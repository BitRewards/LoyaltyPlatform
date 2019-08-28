<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsersLongerPhone extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // $table->text('phone_normalized')->nullable()->change();
            // $table->text('phone_backup')->nullable()->change();
            $table->text('phone')->nullable()->change();

            $table->text('name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // $table->string('phone_normalized', 32)->nullable()->change();
            // $table->string('phone_backup', 32)->nullable()->change();
            $table->string('phone', 32)->nullable()->change();

            $table->string('name', 100)->nullable()->change();
        });
    }
}
