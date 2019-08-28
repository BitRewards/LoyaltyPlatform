<?php

namespace App\Console\Commands\Beanstalkd;

use Illuminate\Console\Command;

class Stats extends Command
{
    protected $signature = 'beanstalkd:stats {queue=default}';
    protected $description = 'Jobs stats';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $queue = $this->argument('queue');
        $pheanstalk = \Queue::getPheanstalk();
        $stats = (array) $pheanstalk->statsTube($queue);

        foreach ($stats as $key => $value) {
            echo "$key = $value\n";
        }

//        $nextJob = (array)$pheanstalk->peekReady($queue);
//        foreach ($nextJob as $key => $value) {
//            echo "$key = $value\n";
//        }
    }
}
