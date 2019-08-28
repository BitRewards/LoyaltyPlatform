<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AuditsAddUpdatedAt extends Migration
{
    public function up()
    {
        \Schema::table('audits', function (Blueprint $table) {
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
    }
}
