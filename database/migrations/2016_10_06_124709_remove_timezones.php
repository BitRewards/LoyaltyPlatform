<?php

use Illuminate\Database\Migrations\Migration;

class RemoveTimezones extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::beginTransaction();

        $tables = DB::select(DB::raw("
            SELECT table_schema,table_name
            FROM information_schema.tables
            WHERE table_schema = 'public'
            ORDER BY table_schema,table_name;
        "));

        foreach ($tables as $row) {
            $tableName = $row->table_name;
            $columns = \Schema::getColumnListing($tableName);

            foreach ($columns as $column) {
                $type = \Schema::getColumnType($tableName, $column);

                if ('datetimetz' == $type) {
                    \DB::select(\DB::raw("ALTER TABLE $tableName ALTER $column TYPE timestamp;"));
                }
            }
        }

        DB::commit();
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::beginTransaction();

        DB::commit();
    }
}
