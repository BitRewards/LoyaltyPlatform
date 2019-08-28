<?php

use Illuminate\Database\Migrations\Migration;

class BitrewardsUpdLocalEnv extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (\App::isLocal()) {
            DB::statement("UPDATE partners SET partner_settings = jsonb_set(partner_settings, '{bitrewards-enabled}', 'true'::jsonb);");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
    }
}
