<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAuditsUserAgentColumn extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->string('user_agent')->after('ip_address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->dropColumn('user_agent');
        });
    }
}
