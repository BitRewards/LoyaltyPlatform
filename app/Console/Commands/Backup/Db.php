<?php

namespace App\Console\Commands\Backup;

use Illuminate\Console\Command;

class Db extends Command
{
    protected $signature = 'backup:db';

    protected $description = 'Backup db to aws';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $path = base_path();
        exec("cd $path");
        exec('pg_dump -U crm_user -h db crm | gzip > backup.gz');

        $s3 = app('aws')->createClient('s3');
        $s3->putObject(array(
            'Bucket' => '**REMOVED**',
            'Key' => 'db_backup_'.date('d.m.Y_H:i:s').'.gz',
            'SourceFile' => base_path('backup.gz'),
        ));

        exec('rm backup');
    }
}
