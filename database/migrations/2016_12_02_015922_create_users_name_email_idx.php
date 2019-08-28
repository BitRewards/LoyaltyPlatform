<?php

use Illuminate\Database\Migrations\Migration;

class CreateUsersNameEmailIdx extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \DB::statement('CREATE INDEX users__name_lower__idx ON users (lower(name));');
        \DB::statement('CREATE INDEX users__email_lower__idx ON users (lower(email));');

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \DB::statement('DROP INDEX users__name_lower__idx;');
        \DB::statement('DROP INDEX users__email_lower__idx ;');

        DB::commit();
    }
}
