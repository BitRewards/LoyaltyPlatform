<?php

use Illuminate\Database\Migrations\Migration;

class RenameActionsDisplayToUsersColumn extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        DB::statement('ALTER TABLE actions RENAME is_displayed_to_users TO is_system');
        DB::statement('ALTER TABLE actions ALTER COLUMN is_system SET DEFAULT FALSE');
        DB::statement('UPDATE actions SET is_system = NOT is_system');

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        DB::statement('ALTER TABLE actions RENAME is_system TO is_displayed_to_users');
        DB::statement('ALTER TABLE actions ALTER COLUMN is_displayed_to_users SET DEFAULT TRUE');
        DB::statement('UPDATE actions SET is_displayed_to_users = NOT is_displayed_to_users');

        DB::commit();
    }
}
