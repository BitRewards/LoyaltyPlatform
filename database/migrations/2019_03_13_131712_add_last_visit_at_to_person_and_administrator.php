<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastVisitAtToPersonAndAdministrator extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->timestamp('last_visited_at')->nullable();
        });

        Schema::table('administrators', function (Blueprint $table) {
            $table->timestamp('last_visited_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->dropColumn('last_visited_at');
        });

        Schema::table('administrators', function (Blueprint $table) {
            $table->dropColumn('last_visited_at');
        });
    }
}
