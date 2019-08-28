<?php

use Illuminate\Database\Migrations\Migration;

class UsersNameTrgrmLowerIdx extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \DB::select(\DB::raw('DROP INDEX users_name_trgm_idx;'));

        \DB::select(\DB::raw('CREATE INDEX users_name_trgm_idx ON users USING GIN (LOWER(name) gin_trgm_ops);'));

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \DB::select(\DB::raw('DROP INDEX users_name_trgm_idx;'));

        \DB::select(\DB::raw('CREATE INDEX users_name_trgm_idx ON users USING GIN (name gin_trgm_ops);'));

        DB::commit();
    }
}
