<?php

use Illuminate\Database\Migrations\Migration;

class ChangeLongIpToCommonIpAddress extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::beginTransaction();
        DB::statement('ALTER TABLE auth_tokens ALTER COLUMN token TYPE text USING token::text');
        DB::statement('ALTER TABLE auth_tokens RENAME COLUMN long_ip TO ip');
        DB::statement('ALTER TABLE auth_tokens ALTER COLUMN ip TYPE text USING ip::text');
        DB::commit();
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::beginTransaction();
        DB::statement('ALTER TABLE auth_tokens ALTER COLUMN token TYPE varchar(255) USING token::varchar(255)');
        DB::statement('ALTER TABLE auth_tokens RENAME COLUMN ip TO long_ip');
        DB::statement('ALTER TABLE auth_tokens ALTER COLUMN long_ip TYPE bigint USING long_ip::bigint');
        DB::commit();
    }
}
