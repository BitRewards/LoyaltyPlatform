<?php

use Illuminate\Database\Migrations\Migration;

class UserCreateTrigramIndexes extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        \DB::select(\DB::raw('CREATE EXTENSION IF NOT EXISTS pg_trgm;'));

        \DB::select(\DB::raw('CREATE INDEX users_name_trgm_idx ON users USING GIN (name gin_trgm_ops);'));

        DB::commit();
    }

    public function down()
    {
        DB::beginTransaction();

        \DB::select(\DB::raw('drop extension pg_trgm CASCADE;'));

        \DB::statement('DROP INDEX IF EXISTS users_name_trgm_idx;');

        DB::commit();
    }
}
