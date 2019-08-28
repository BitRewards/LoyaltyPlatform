<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPartnerGroupRole extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->string('partner_group_role')->default('partner')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn('partner_group_role');
        });
    }
}
