<?php

namespace App\Console\Commands\Db;

use Illuminate\Console\Command;

class IndexForeignKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:indexForeignKeys';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $foreignKeyColumns = $this->getForeignKeyColumns();
        $indexedColumns = $this->getIndexedColumns();

        $columnsWithoutIndex = [];

        foreach ($foreignKeyColumns as $table => $columns) {
            foreach ($columns as $column) {
                if (!isset($indexedColumns[$table][$column])) {
                    $columnsWithoutIndex[$table][] = $column;
                }
            }
        }

        $totalIndexesCreatedCount = $this->createIndex($columnsWithoutIndex);

        if ($totalIndexesCreatedCount) {
            $this->info("Successfully created {$totalIndexesCreatedCount} index(es)!");
        } else {
            $this->info('There is no unindexed foreign keys!');
        }
    }

    // [tablename] => [fkColumn, ...]
    private function getForeignKeyColumns()
    {
        $foreignKeyColumns = [];
        $keyColumns = \DB::select('SELECT * FROM information_schema.key_column_usage');

        foreach ($keyColumns as $keyColumn) {
            if ($this->endsWith($keyColumn->constraint_name, 'foreign')) {
                $foreignKeyColumns[$keyColumn->table_name][] = $keyColumn->column_name;
            }
        }

        return $foreignKeyColumns;
    }

    // [table][column] => true/false
    private function getIndexedColumns()
    {
        $indexedColumns = [];
        $indexes = \DB::select('SELECT tablename, indexdef FROM pg_catalog.pg_indexes WHERE schemaname = ?', ['public']);

        foreach ($indexes as $index) {
            $openBracketPos = strpos($index->indexdef, '(');
            $columns = substr($index->indexdef, $openBracketPos + 1, -1);
            $column = explode(',', $columns)[0];
            $indexedColumns[$index->tablename][$column] = true;
        }

        return $indexedColumns;
    }

    // [table] => [column, ...]
    private function createIndex($columns)
    {
        $totalIndexesCreatedCount = 0;

        foreach ($columns as $table => $columns) {
            foreach ($columns as $column) {
                \DB::statement("CREATE INDEX {$table}_{$column}_index ON {$table} USING btree ({$column})");

                ++$totalIndexesCreatedCount;

                $this->info("Created missing index for column {$column} on table {$table}");
            }
        }

        return $totalIndexesCreatedCount;
    }

    private function startsWith($haystack, $needle)
    {
        $length = strlen($needle);

        return substr($haystack, 0, $length) === $needle;
    }

    private function endsWith($haystack, $needle)
    {
        $length = strlen($needle);

        if (0 == $length) {
            return true;
        }

        return substr($haystack, -$length) === $needle;
    }
}
