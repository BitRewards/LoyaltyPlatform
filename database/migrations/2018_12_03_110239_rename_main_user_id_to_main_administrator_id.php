<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameMainUserIdToMainAdministratorId extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->renameColumn('main_user_id', 'main_administrator_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->renameColumn('main_administrator_id', 'main_user_id');
        });
    }
}
