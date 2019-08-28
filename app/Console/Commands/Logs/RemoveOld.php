<?php

namespace App\Console\Commands\Logs;

use Illuminate\Console\Command;

class RemoveOld extends Command
{
    protected $signature = 'logs:removeOld';

    protected $description = 'Remove old logs';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $logsFolder = storage_path().'/logs/';

        $date = date('Ymd', strtotime('-30 days'));
        $oldFilename = "crm_$date.log";

        $files = array_diff(scandir($logsFolder), ['.', '..']);

        foreach ($files as $file) {
            $isFileCrmLog = 0 === strpos($file, 'crm_');

            if ($isFileCrmLog) {
                $isFileOld = $file <= $oldFilename;

                if ($isFileOld) {
                    unlink("$logsFolder/$file");
                }
            }
        }

        echo "Old logs are removed!\n";
    }
}
