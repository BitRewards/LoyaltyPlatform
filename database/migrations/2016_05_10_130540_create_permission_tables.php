<?php

use Illuminate\Database\Migrations\Migration;

class CreatePermissionTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('user_has_roles');
        Schema::dropIfExists('user_has_permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
    }
}
