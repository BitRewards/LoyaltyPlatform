<?php

namespace App\Console\Commands\Db;

use Illuminate\Console\Command;

class ListForeignKeys extends Command
{
    protected $signature = 'db:listForeignKeys';

    protected $description = 'Lists foreign keys (for dev purposes)';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $substr = 'user_id';
        $foreignKeyColumns = [];
        $keyColumns = \DB::select('SELECT * FROM information_schema.key_column_usage');

        foreach ($keyColumns as $keyColumn) {
            if ($this->endsWith($keyColumn->constraint_name, 'foreign') && false !== strpos($keyColumn->constraint_name, $substr)) {
                $this->info("Table {$keyColumn->table_name} has foreign key {$keyColumn->constraint_name}");
            }
        }
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
